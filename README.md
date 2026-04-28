# Crédinor — Sistema de Gestión de Créditos y Cobranzas

Crédinor es una plataforma web integral para la gestión de microcréditos, préstamos y cobranzas diarias. Arquitectura MVC propia en PHP 8.2 con MySQL, Tailwind CSS 3 y Alpine.js.

---

## Características Principales

### Administrador
- **Dashboard** con KPIs financieros en tiempo real (Chart.js).
- **Créditos:** aprobación, rechazo, edición y seguimiento del ciclo completo.
- **Pagos:** listado global, anulación con motivo y auditoría.
- **Rendiciones:** validación cruzada de cajas de cobradores.
- **Reportes:** cartera, mora y productividad de cobradores.
- **Usuarios y Sucursales:** gestión de accesos y distribución geográfica.

### Vendedor
- Alta de clientes con datos personales y garantes.
- Simulador y creación de planes de pago.

### Cobrador
- **Agenda inteligente** con rutas por vencimiento y atraso.
- Registro de pagos móvil (totales o parciales).
- **Cierre de caja ciego** (declara antes de ver el total del sistema).
- Historial, recibos PDF y rendiciones.

---

## Stack Tecnológico

| Capa | Tecnología |
|---|---|
| Backend | PHP 8.2, MVC propio, PDO (prepared statements) |
| Routing | nikic/fast-route |
| PDF | dompdf/dompdf |
| Frontend | Tailwind CSS 3.4, Alpine.js 3, Chart.js 4.4 |
| Fuentes | Outfit + Inter (Google Fonts) |
| Iconos | Iconsax (autohospedado en `public/assets/vendor/`) |
| Tests | PHPUnit 11 |

---

## Instalación

### Requisitos
- Apache/Nginx con `mod_rewrite` habilitado.
- PHP 8.2+ con extensiones: `pdo_mysql`, `mbstring`.
- MySQL 8.0+ o MariaDB 10.6+.
- Composer y Node.js 18+.

### 1. Clonar y configurar entorno

```bash
git clone git@github.com:danqueve/credinor.git
cd credinor

# Copiar el archivo de entorno
cp .env.example .env
# Editar .env con tus credenciales de DB y URL
```

### 2. Instalar dependencias

```bash
composer install --optimize-autoloader
npm install
npm run build
```

### 3. Aplicar migraciones

```bash
# Ver estado
php migrate.php --status

# Aplicar todas las pendientes
php migrate.php
```

> Las migraciones se aplican en orden y se registran en la tabla `migrations` para no repetirse.

### 4. Configurar Apache (VirtualHost o .htaccess)

El DocumentRoot debe apuntar a `public/`. El `.htaccess` incluido maneja el front controller.

---

## Tareas Programadas (Cron Jobs)

Configurar en cPanel/crontab para ejecución diaria (madrugada):

```bash
# Devengar mora diaria — 00:01
1 0 * * * php /ruta/credinor/cron/devengar_mora.php >> /var/log/credinor_mora.log 2>&1

# Actualizar estados de cuotas — 00:02
2 0 * * * php /var/credinor/cron/actualizar_estados.php >> /var/log/credinor_estados.log 2>&1

# Backup diario — 01:30
30 1 * * * php /ruta/credinor/cron/backup.php >> /var/log/credinor_backup.log 2>&1
```

Los cron jobs incluyen **lock files** para prevenir ejecuciones concurrentes.

---

## Acceso Inicial

Al aplicar `000_full_install.sql`, se crea el usuario administrador:

- **Usuario:** `admin`
- **Contraseña:** `Admin1234!`

Cambiar la contraseña en el primer ingreso desde Perfil > Seguridad.

---

## Tests

```bash
vendor/bin/phpunit --testdox
```

Suite de tests unitarios cubre `MoneyHelper`, `DateHelper` y validación de seguridad en `Model::all()`.

---

## Seguridad

- Credenciales via `.env` (nunca hardcodeadas).
- PDO con prepared statements (sin SQL injection).
- CSRF en todos los formularios POST.
- Rate limiting en login: bloqueo de 15 min tras 5 intentos fallidos.
- Headers HTTP: CSP, HSTS, X-Frame-Options, X-Content-Type-Options.
- Bcrypt cost 12 para contraseñas.
- Log de autenticación en `auth_log`.

---

*PHP 8.2 · Tailwind CSS 3 · Alpine.js 3 · PHPUnit 11*
