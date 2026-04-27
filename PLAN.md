# Sistema de Préstamos — Plan de Diseño

## 1. Resumen ejecutivo

Sistema web para gestión de préstamos en efectivo con cobranza presencial. Tres roles operativos (admin, vendedor, cobrador), multi-sucursal con datos compartidos entre admins. El vendedor carga la solicitud, el admin la autoriza y asigna cobrador, el cobrador trabaja con su agenda diaria/semanal y rinde caja al cierre del día.

Independiente de SAS Imperio. Repo nuevo, base de datos nueva, dominio/subdominio aparte.

---

## 2. Stack técnico

| Capa | Tecnología | Justificación |
|------|------------|---------------|
| Backend | PHP 8.2 | Compatibilidad con VPS (cPanel/AlmaLinux) y experiencia previa |
| ORM/DB | PDO + MySQL 8 | Sin ORM pesado: control total sobre queries críticas (mora, agenda) |
| Routing | FastRoute o router propio | Liviano, compatible con PSR-4 |
| Vistas | PHP nativo + componentes parciales | Sin engine extra; suficiente y rápido |
| CSS | Tailwind CSS 3 | Mobile-first nativo, ideal para agenda del cobrador en celular |
| JS | Alpine.js 3 + vanilla | Interactividad sin SPA |
| PDF | DomPDF | Recibos de pago y reportes |
| Mapas | Leaflet + OpenStreetMap | Gratis y suficiente para mostrar ubicación cliente |
| Autoload | Composer (PSR-4) | Estándar |
| Build | npm + Tailwind CLI | Compilar CSS de producción |

### Argumentación CSS
**Tailwind sobre Bootstrap:** el panel del cobrador es lo más usado y se opera 100% en celular. Tailwind permite componentes propios bien tipográficos sin pelearse con grids genéricos. El admin en PC reusa los mismos utilitarios.

---

## 3. Roles y permisos

### Administrador
- CRUD completo de usuarios, sucursales, clientes, créditos
- Autoriza/rechaza créditos pendientes
- Asigna cobrador al autorizar
- Confirma rendiciones de caja
- Configura mora global y por crédito
- Ve reportes globales (todas las sucursales)
- Puede condonar mora, anular pagos, refinanciar

### Vendedor
- CRUD de clientes (de su sucursal)
- Crea solicitudes de crédito (estado inicial: `pendiente_autorizacion`)
- Ve estado de sus solicitudes
- No autoriza, no asigna cobrador, no ve reportes globales

### Cobrador
- Ve su agenda del día/semana/quincena/mes según frecuencia de cada crédito
- Registra pagos (totales o parciales)
- Cierra caja al final del día (rendición)
- Ve historial de cobros propios
- No carga clientes ni crea créditos

---

## 4. Modelo de datos

Ver `schema.sql` para el DDL completo. Tablas principales:

| Tabla | Propósito |
|-------|-----------|
| `sucursales` | Multi-sucursal |
| `usuarios` | Admin / vendedor / cobrador |
| `clientes` | Datos personales y geolocalización |
| `garantes` | Opcional, reusable |
| `creditos` | Préstamos otorgados |
| `cuotas` | Plan de pagos generado al autorizar |
| `pagos` | Registro de cobros (puede haber varios por cuota) |
| `rendiciones` | Cierre de caja diario del cobrador |
| `mora_devengada` | Auditoría diaria del cron de mora |
| `creditos_log` | Auditoría de cambios de estado |
| `config` | Parámetros globales (porcentaje de mora, etc.) |

### Decisiones clave
- **Mora**: bolsa aparte (`creditos.mora_acumulada`), no se mete en cuotas. Cron diario devenga sobre cuotas vencidas y guarda registro en `mora_devengada` para auditoría.
- **Pagos parciales**: tabla `pagos` con FK a `cuotas`. Estado de cuota = suma de pagos vs `monto`.
- **Imputación**: capital primero. Cada `pago` tiene `monto_a_capital` y `monto_a_mora` separados.
- **Rendición**: pago nace `pendiente_rendir` → cobrador cierra día → admin confirma → pago pasa a `confirmado`.

---

## 5. Flujos principales

### 5.1 Alta de crédito
1. Vendedor busca cliente por DNI; si no existe lo carga.
2. Carga solicitud: monto prestado, monto a devolver, cuotas, frecuencia, fechas, garante (opcional).
3. Sistema valida (`monto_a_devolver >= monto_prestado`, `cantidad_cuotas > 0`, etc.).
4. Crédito queda en `pendiente_autorizacion`.

### 5.2 Autorización
1. Admin ve listado de créditos pendientes (de todas las sucursales).
2. Revisa datos del cliente y del crédito.
3. Aprueba o rechaza. Si aprueba: asigna cobrador y el sistema genera las cuotas automáticamente según frecuencia.
4. Estado pasa a `activo`.

### 5.3 Generación de cuotas
- **Diaria**: cada vencimiento +1 día.
- **Semanal**: cada vencimiento +7 días.
- **Quincenal**: cada vencimiento +15 días.
- **Mensual**: cada vencimiento +1 mes (mismo día; si no existe en ese mes, último día disponible).
- `monto_cuota = monto_a_devolver / cantidad_cuotas` (con redondeo y ajuste en última cuota).

### 5.4 Cobro
1. Cobrador entra a su agenda → ve clientes a visitar (ordenados por prioridad: hoy → atrasadas).
2. Tap en cliente → ficha con botón WhatsApp, link Google Maps, monto adeudado.
3. Registra pago: ingresa monto. Si hay mora, marca cuánto va a mora y cuánto a capital (default: todo a capital).
4. Pago queda en `pendiente_rendir`.

### 5.5 Devengamiento de mora (cron diario 00:01)
```
Para cada cuota vencida con saldo > 0 y crédito activo con aplica_mora = true:
    saldo = monto - monto_pagado
    porcentaje = credito.porcentaje_mora_diaria || config.porcentaje_mora_diaria_default
    mora_dia = saldo * porcentaje / 100
    INSERT INTO mora_devengada
    UPDATE creditos SET mora_acumulada = mora_acumulada + mora_dia
```
Idempotente: la unique key `(cuota_id, fecha)` evita doble cargo si el cron corre dos veces.

### 5.6 Cierre de caja
1. Cobrador entra a "Cerrar día" → ve listado de pagos de la jornada con total.
2. Confirma → genera `rendicion` con `monto_declarado` y todos sus pagos asociados.
3. Admin recibe rendición → ingresa `monto_recibido` real → confirma o rechaza.
4. Si confirma: pagos pasan a `confirmado`. Si hay diferencia, queda registrada.

### 5.7 Cierre de crédito
- Cuando todas las cuotas están `pagada` y `mora_pagada >= mora_acumulada` (o admin condona), el crédito pasa a `finalizado` automáticamente.

---

## 6. Reglas de negocio

| Regla | Definición |
|-------|------------|
| Diferencia | `monto_a_devolver - monto_prestado`, visible en ficha del crédito |
| Mora | `% diario sobre saldo de cuota vencida`, configurable global o por crédito |
| Imputación | Capital primero, mora aparte |
| Pagos parciales | Permitidos; cuota queda en estado `parcial` |
| Estados de cuota | `pendiente` → `parcial` → `pagada`, o `vencida` si pasó la fecha |
| Estados de crédito | `pendiente_autorizacion` → `autorizado` → `activo` → `finalizado` (o `rechazado` / `cancelado`) |
| Multi-sucursal | Vendedor y cobrador limitados a su sucursal; admin ve todo |

---

## 7. Estructura de carpetas

```
prestamos/
├── public/                       webroot (DocumentRoot)
│   ├── index.php                 front controller
│   ├── .htaccess
│   └── assets/
│       ├── css/app.css           Tailwind compilado
│       ├── js/app.js
│       └── img/
├── src/
│   ├── Config/
│   │   ├── database.php
│   │   └── app.php
│   ├── Core/
│   │   ├── Router.php
│   │   ├── Database.php          PDO singleton
│   │   ├── Controller.php
│   │   ├── Model.php
│   │   ├── Auth.php
│   │   ├── Session.php
│   │   ├── Request.php
│   │   ├── Response.php
│   │   └── View.php
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ClientesController.php
│   │   ├── CreditosController.php
│   │   ├── CuotasController.php
│   │   ├── PagosController.php
│   │   ├── RendicionesController.php
│   │   ├── UsuariosController.php
│   │   └── ReportesController.php
│   ├── Models/
│   │   ├── Usuario.php
│   │   ├── Cliente.php
│   │   ├── Credito.php
│   │   ├── Cuota.php
│   │   ├── Pago.php
│   │   ├── Rendicion.php
│   │   ├── Garante.php
│   │   └── Sucursal.php
│   ├── Services/
│   │   ├── CreditoService.php    autorización + generación de cuotas
│   │   ├── PagoService.php       imputación capital/mora
│   │   ├── MoraService.php       cálculo y devengamiento
│   │   ├── RendicionService.php  cierre de caja
│   │   └── ReporteService.php
│   ├── Middleware/
│   │   ├── AuthMiddleware.php
│   │   └── RolMiddleware.php
│   └── Helpers/
│       ├── DateHelper.php        cálculo de vencimientos por frecuencia
│       ├── MoneyHelper.php
│       └── PhoneHelper.php       formato whatsapp
├── views/
│   ├── layouts/
│   │   ├── app.php
│   │   ├── auth.php
│   │   └── partials/{nav,sidebar,flash}.php
│   ├── auth/login.php
│   ├── admin/{dashboard,creditos_pendientes,usuarios,sucursales,rendiciones,reportes}.php
│   ├── vendedor/{dashboard,cliente_form,credito_form}.php
│   ├── cobrador/{dashboard,pago_form,cierre_caja}.php
│   └── shared/{credito_detalle,cliente_detalle}.php
├── cron/
│   ├── devengar_mora.php         00:01 diario
│   └── actualizar_estados.php    marca cuotas vencidas
├── migrations/
│   ├── 001_schema.sql
│   └── 002_seed.sql
├── storage/
│   ├── logs/
│   └── recibos_pdf/
├── vendor/                       composer
├── composer.json
├── tailwind.config.js
├── package.json
└── README.md
```

---

## 8. Plan de fases

### Fase 1 — Fundación (semana 1)
- Estructura de carpetas y Composer
- Conexión PDO + helpers core (Router, Auth, Session, View)
- Login con bcrypt + roles
- Layout base con Tailwind y navegación por rol
- Crear schema en MySQL y usuario admin inicial

### Fase 2 — Gestión de clientes y créditos (semana 2)
- CRUD de clientes (con captura de lat/lng vía Leaflet)
- Formulario de solicitud de crédito (vendedor)
- Listado de pendientes y autorización (admin)
- Generación automática de cuotas según frecuencia
- Auditoría en `creditos_log`

### Fase 3 — Cobranza (semana 3)
- Agenda del cobrador (vista mobile-first con tabs por prioridad)
- Registro de pago total y parcial
- Imputación capital/mora
- Cron de devengamiento diario de mora
- Recibo PDF descargable

### Fase 4 — Rendiciones y cierre (semana 4)
- Cierre de caja del cobrador
- Confirmación por admin con detección de diferencias
- Estados de pago: `pendiente_rendir` → `rendido` → `confirmado`
- Reporte diario de caja

### Fase 5 — Reportes y mejoras (semana 5)
- Cartera por cobrador, sucursal, vendedor
- Mora consolidada, recupero, vencimientos próximos
- Exportación a Excel
- Notificaciones automáticas WhatsApp (opcional, vía link directo)

### Fase 6 — Despliegue
- Deploy en VPS Dattaweb (subdominio nuevo)
- Configuración de cron en cPanel
- Backups automáticos diarios
- Documentación de uso para cada rol

---

## 9. Pendientes operativos para definir

- ¿Múltiples créditos activos por cliente o solo uno a la vez?
- ¿Cancelación anticipada con descuento de intereses?
- ¿Refinanciación (nuevo crédito que cancela el viejo)?
- ¿Notificaciones automáticas WhatsApp (recordatorios)?
- ¿Recibo PDF impreso en papel térmico desde el celular del cobrador?
- ¿Foto de comprobante adjunta al pago?

Esto se puede ir resolviendo mientras avanzamos las fases.
