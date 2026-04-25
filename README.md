# 💰 Crédinor - Sistema de Gestión de Créditos y Cobranzas

Crédinor es una plataforma integral basada en la web para la gestión eficiente de microcréditos, préstamos y cobranzas diarias. Diseñada con una arquitectura MVC robusta en PHP, la plataforma ofrece herramientas especializadas para administradores, vendedores y cobradores de terreno.

## ✨ Características Principales

El sistema está dividido en tres perfiles operativos principales:

### 👑 Administrador (Admin)
- **Dashboard SaaS:** Visualización en tiempo real de KPIs financieros (Capital activo, mora pendiente, cobrado hoy, rendiciones pendientes).
- **Gestión de Créditos:** Aprobación, rechazo y seguimiento del ciclo de vida completo de cada préstamo.
- **Auditoría de Rendiciones:** Validación cruzada de las cajas cerradas por los cobradores contra los pagos físicos registrados en el sistema.
- **Reportes Avanzados:** 
  - *Cartera de Créditos:* Análisis de capital activo con filtros por sucursal.
  - *Reporte de Mora:* Ranking de morosidad y KPIs de salud financiera.
  - *Productividad:* Evaluación de rendimiento y ranking mensual de cobradores.
- **Gestión de Usuarios y Sucursales:** Control total sobre los accesos y la distribución geográfica del negocio.

### 💼 Vendedor
- **Captación de Clientes:** Ficha detallada de clientes con validación de datos personales, laborales, referencias e historial crediticio.
- **Simulador y Venta:** Creación rápida de planes de pago y otorgamiento de créditos.

### 🏍️ Cobrador
- **Agenda Inteligente:** Rutas de cobranza generadas automáticamente según los vencimientos del día y atrasos acumulados.
- **Registro Móvil de Pagos:** Captura de cobros en terreno (totales o parciales) optimizada para dispositivos móviles.
- **Cierre de Caja Ciego:** Sistema de seguridad donde el cobrador declara lo recaudado antes de ver el monto total del sistema, asegurando transparencia financiera.
- **Historial y Recibos:** Generación de comprobantes y recibos en PDF.

---

## 🛠️ Stack Tecnológico

- **Backend:** PHP 8.1+ (Arquitectura MVC Propia PDO).
- **Base de Datos:** MySQL / MariaDB.
- **Frontend / UI:** 
  - **Tailwind CSS v4** (Diseño *Clean SaaS* / *Glassmorphism*).
  - **Alpine.js** (Interactividad ligera y reactividad sin frameworks pesados).
  - **Fuentes:** Outfit (Encabezados) e Inter (Datos).
  - **Iconos:** Iconsax Premium.

---

## 🚀 Guía de Instalación

### 1. Requisitos Previos
- Servidor Web (Apache/Nginx).
- PHP 8.1 o superior (Extensiones: `pdo_mysql`, `mbstring`).
- MySQL 5.7+ o MariaDB 10.3+.
- Composer & Node.js (Opcional, para compilar assets).

### 2. Clonar el repositorio
```bash
git clone git@github.com:danqueve/credinor.git
cd credinor
```

### 3. Configuración del Entorno
Duplica el archivo de configuración base:
```bash
cp src/Config/database.example.php src/Config/database.php
# O crea tu propio archivo .env si implementas DotEnv
```
Asegúrate de configurar los datos de acceso a tu base de datos local (ej. `a0040079_credinor`).

### 4. Base de Datos
Importa los archivos de migración en orden estricto dentro de tu gestor (phpMyAdmin, DBeaver, etc.):
1. `migrations/001_schema.sql` (Estructura base)
2. `migrations/002_seed.sql` (Datos iniciales y superusuario)
3. `migrations/003_mora_rendiciones.sql` (Tablas para auditoría de cajas)
4. `migrations/004_username.sql` (Actualización de login de usuarios)

### 5. Compilar Estilos (Opcional)
Si modificas el archivo `public/assets/css/app.src.css`, debes recompilar Tailwind:
```bash
npm install
npm run build
```

---

## ⏱️ Tareas Programadas (Cron Jobs)

Para el correcto funcionamiento financiero del sistema, es **obligatorio** configurar las siguientes tareas en el servidor (cPanel / Crontab) para que se ejecuten todos los días (preferentemente de madrugada):

1. **Devengar Mora Diaria:** (Recomendado: `00:30 AM`)
```bash
php /ruta/a/credinor/cron/devengar_mora.php
```

2. **Actualizar Estados (Vencimientos):** (Recomendado: `01:00 AM`)
```bash
php /ruta/a/credinor/cron/actualizar_estados.php
```

---

## 🔐 Acceso de Prueba

Al instalar la base de datos con los seeds (`002_seed.sql`), se crea un usuario administrador por defecto:
- **Usuario:** `admin`
- **Contraseña:** `admin123`

*(Por motivos de seguridad, cambia esta contraseña inmediatamente después del primer inicio de sesión).*

---
*Desarrollado con arquitectura a medida para escalabilidad financiera.*
