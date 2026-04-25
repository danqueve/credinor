# Rediseño: roles unificados, método de pago, mora opcional, historial de rendiciones

## Context

Cuatro cambios estructurales solicitados sobre el sistema actual de Credinor:

1. **Roles unificados**: `cobrador` y `vendedor` deben compartir todas las funciones operativas. Hoy el sistema los trata como roles disjuntos: distintos menús, distintas URLs (`/cobrador/*` vs `/vendedor/*`), y `requireRole('cobrador')` bloquea al vendedor en pagos y agenda. La intención es que cualquier integrante de campo pueda dar de alta clientes, generar créditos, cobrar cuotas y cerrar caja.
2. **Método de pago**: al registrar un pago se debe elegir entre `efectivo` o `transferencia`. La tabla `pagos` no tiene esa columna hoy.
3. **Mora opcional por defecto**: hoy `creditos.aplica_mora` defaultea a `1`. La política de negocio cambió: por defecto los créditos **no** generan mora. Si una cuota se atrasa, el cliente paga el monto normal de la cuota.
4. **Historial de rendiciones para cobradores**: el admin ya ve toda la historia de rendiciones, pero el cobrador solo ve la del día (`Rendicion::getDeHoy()` en `cierre_caja.php`). Se necesita una vista persistente accesible al cobrador.

Outcome esperado: un único perfil operativo "staff" (cobrador+vendedor) con menú unificado, pagos con método registrado, mora desactivada por defecto, y rendiciones siempre auditables desde la cuenta del cobrador.

---

## Approach

Se mantiene el ENUM `usuarios.rol` con los tres valores (`admin`, `vendedor`, `cobrador`) para no romper datos existentes. Operativamente se introduce el concepto **"staff"** = `[cobrador, vendedor]` mediante un helper, y todos los endpoints operativos aceptan ambos roles. El menú lateral pasa a ser único para staff.

---

## Cambio 1 — Unificación de roles (cobrador ↔ vendedor)

### 1.1 Helpers en `Auth` y `Controller`

**[src/Core/Auth.php](src/Core/Auth.php)** — agregar:
```php
public const STAFF = ['cobrador', 'vendedor'];

public static function isStaff(): bool
{
    return in_array(self::rol(), self::STAFF, true);
}
```

**[src/Core/Controller.php:requireRole](src/Core/Controller.php)** — agregar atajo:
```php
protected function requireStaff(): void
{
    $this->requireRole(Auth::STAFF);
}
```
(`requireRole` ya acepta `array|string`, así que no hace falta cambiar la firma.)

### 1.2 Reemplazar `requireRole('cobrador')` en controladores operativos

Cambiar en los 6 sitios identificados (todos a `$this->requireStaff()`):

- [src/Controllers/PagosController.php:29](src/Controllers/PagosController.php#L29) `form()`
- [src/Controllers/PagosController.php:58](src/Controllers/PagosController.php#L58) `store()`
- [src/Controllers/CobradorController.php:20](src/Controllers/CobradorController.php#L20) `agenda()`
- [src/Controllers/CobradorController.php:39](src/Controllers/CobradorController.php#L39) `historial()`
- [src/Controllers/CobradorController.php:47](src/Controllers/CobradorController.php#L47) `caja()`
- [src/Controllers/CobradorController.php:62](src/Controllers/CobradorController.php#L62) `cerrarCaja()`

Adicionalmente **agregar `requireStaff()`** donde hoy no hay chequeo (los `/vendedor/*` solo dependen de `AuthMiddleware`):
- `ClientesController` — métodos que sirven `/vendedor/clientes/*` y `/api/clientes/buscar`
- `CreditosController::misCreditos`, `nuevo`, `store`
- `PagosController::recibo` (hoy lo abre cualquier autenticado — gate a staff o admin)

Dejar tal cual los `requireRole('admin')` (22 sitios — son correctos).

### 1.3 Sidebar unificado

**[views/layouts/partials/sidebar.php](views/layouts/partials/sidebar.php)** (líneas 3–33). Reemplazar `$navVendedor` y `$navCobrador` por un único `$navStaff`:

```php
$navStaff = [
    ['href' => '/dashboard',          'icon' => 'isax-home-2',        'label' => 'Inicio'],
    ['href' => '/cobrador/agenda',    'icon' => 'isax-calendar-1',    'label' => 'Agenda'],
    ['href' => '/vendedor/clientes',  'icon' => 'isax-profile-2user', 'label' => 'Clientes'],
    ['href' => '/vendedor/creditos',  'icon' => 'isax-document-text', 'label' => 'Créditos'],
    ['href' => '/cobrador/caja',      'icon' => 'isax-card-tick',     'label' => 'Cerrar caja'],
    ['href' => '/cobrador/rendiciones','icon'=> 'isax-wallet-money',  'label' => 'Rendiciones'],
    ['href' => '/cobrador/historial', 'icon' => 'isax-clock',         'label' => 'Historial'],
];

$nav = match($rol) {
    'admin'    => $navAdmin,
    'cobrador', 'vendedor' => $navStaff,
    default    => [],
};
```

### 1.4 `getCobradores()` debe incluir vendedores

Para asignación de cobradores a créditos y reportes, ambos roles cuentan:

- **[src/Models/Usuario.php:28-40](src/Models/Usuario.php#L28-L40)** `getCobradores()`: cambiar `WHERE rol = 'cobrador'` por `WHERE rol IN ('cobrador','vendedor')`.
- **[src/Controllers/ReportesController.php:128](src/Controllers/ReportesController.php#L128)** y **[ReportesController.php:266](src/Controllers/ReportesController.php#L266)** (`getCobradores` privado): mismo cambio `IN ('cobrador','vendedor')`.

### 1.5 Helper de layout

- **[src/Controllers/CreditosController.php:90](src/Controllers/CreditosController.php#L90)**: `$layout = Auth::isAdmin() ? 'admin' : (Auth::isVendedor() ? 'vendedor' : 'cobrador');` → simplificar a `Auth::isAdmin() ? 'admin' : 'staff'` y crear o reutilizar un único layout staff (puede ser el mismo `vendedor.php` renombrado, ver siguiente punto).
- **[views/shared/credito_detalle.php:21](views/shared/credito_detalle.php#L21)**: el back-link condicional `Auth::isVendedor() ? url('vendedor/creditos') : url('dashboard')` debe pasar a `Auth::isStaff() ? url('vendedor/creditos') : url('dashboard')`.

Layouts en `views/layouts/`: si existen `vendedor.php` y `cobrador.php` separados, consolidarlos en `staff.php` (o dejar uno y eliminar el otro). **Acción**: revisar `views/layouts/` durante implementación; si son idénticos en estructura, unificar.

---

## Cambio 2 — Método de pago (efectivo / transferencia)

### 2.1 Schema

Agregar columna a `pagos`. Dado que el repo ya maneja migraciones numeradas, crear **`migrations/005_metodo_pago_y_mora_default.sql`** (cubre ambos cambios 2 y 3):

```sql
ALTER TABLE pagos
    ADD COLUMN metodo_pago ENUM('efectivo','transferencia') NOT NULL DEFAULT 'efectivo' AFTER monto_a_mora;

ALTER TABLE creditos
    ALTER COLUMN aplica_mora SET DEFAULT 0;

UPDATE creditos SET aplica_mora = 0;
```

También actualizar **[migrations/001_schema.sql](migrations/001_schema.sql)** (línea ~151) para que un alta limpia incluya la columna desde el inicio.

### 2.2 Form de pago

**[views/cobrador/pago_form.php](views/cobrador/pago_form.php)**: agregar selector de método de pago en el bloque del monto (visualmente debajo del input `monto`, antes del bloque de mora). Patrón sugerido — radio buttons al estilo de los toggle de estado en [views/admin/usuario_form.php:115-130](views/admin/usuario_form.php#L115-L130):

```php
<div>
  <label class="form-label block text-sm font-bold text-slate-700 mb-2">Método de pago <span class="text-red-500">*</span></label>
  <div class="grid grid-cols-2 gap-3">
    <label class="relative cursor-pointer">
      <input type="radio" name="metodo_pago" value="efectivo" class="sr-only peer" checked>
      <div class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl peer-checked:bg-emerald-50 peer-checked:border-emerald-300 peer-checked:text-emerald-700 font-bold text-slate-600 transition-all flex items-center justify-center gap-2">
        <i class="isax isax-money"></i> Efectivo
      </div>
    </label>
    <label class="relative cursor-pointer">
      <input type="radio" name="metodo_pago" value="transferencia" class="sr-only peer">
      <div class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl peer-checked:bg-sky-50 peer-checked:border-sky-300 peer-checked:text-sky-700 font-bold text-slate-600 transition-all flex items-center justify-center gap-2">
        <i class="isax isax-card-send"></i> Transferencia
      </div>
    </label>
  </div>
</div>
```

### 2.3 Controlador

**[src/Controllers/PagosController.php:store()](src/Controllers/PagosController.php#L55-L79)**: leer y validar el campo, pasarlo al servicio:
```php
$metodoPago = Request::post('metodo_pago', 'efectivo');
if (!in_array($metodoPago, ['efectivo','transferencia'], true)) $metodoPago = 'efectivo';
// ...
$pagoId = $this->service->registrar($cuotaId, $monto, $montoMora, $metodoPago);
```

### 2.4 Servicio

**[src/Services/PagoService.php:registrar()](src/Services/PagoService.php#L29)** — extender firma y INSERT:
```php
public function registrar(
    int $cuotaId,
    float $montoIngresado,
    float $montoAMora = 0.0,
    string $metodoPago = 'efectivo'
): int
```

INSERT (líneas 71–82) — agregar `metodo_pago` a la lista de columnas y al `execute()`.

### 2.5 Display en recibo y rendición

- **[views/cobrador/recibo_pdf.php](views/cobrador/recibo_pdf.php)** (bloque destacado ~líneas 83–94): añadir línea "Método: Efectivo / Transferencia" usando `$row['metodo_pago']` (no `$pago`; ver nota de bug).
- **[views/admin/rendicion_detalle.php](views/admin/rendicion_detalle.php)** (tabla desglose ~líneas 137–166): añadir columna "Método" entre cliente y monto, con badge color (verde efectivo, celeste transferencia). Aporta a la conciliación: el admin ve cuánto entró por cada vía.
- **[views/cobrador/historial.php:113-122](views/cobrador/historial.php#L113-L122)**: opcional — pequeño badge debajo del monto.

### 2.6 Bug colateral en `recibo()`

**[src/Controllers/PagosController.php:82-106](src/Controllers/PagosController.php#L82-L106)** define `$row` pero el template `recibo_pdf.php` usa `$pago`. Esto ya está roto hoy (recibo probablemente sale con campos vacíos). Aprovechar la edición para renombrar `$row` → `$pago` en el controller, ya que el template se va a tocar para `metodo_pago` igual.

`Pago::getPagoConDetalles()` ya hace `SELECT p.*` así que `metodo_pago` se materializa automáticamente — no requiere cambios en el modelo.

---

## Cambio 3 — Mora desactivada por defecto

Tres capas defaultean a `1` hoy. Cambiar las tres a `0`:

### 3.1 Schema

Cubierto por `migrations/005_metodo_pago_y_mora_default.sql` (ver sección 2.1):
- `ALTER TABLE creditos ALTER COLUMN aplica_mora SET DEFAULT 0;`
- `UPDATE creditos SET aplica_mora = 0;` ← aplica a todos los créditos existentes según pedido del usuario ("todos los clientes no se les cobra interés"). **No** se modifica `mora_acumulada` ya devengada (registro contable histórico).

También en **[migrations/001_schema.sql:99](migrations/001_schema.sql#L99)**: cambiar `DEFAULT 1` → `DEFAULT 0` para futuras instalaciones limpias.

### 3.2 Service-layer fallback

**[src/Services/CreditoService.php:67](src/Services/CreditoService.php#L67)**:
```php
':aplica_mora' => (int) ($data['aplica_mora'] ?? 0),
```

### 3.3 Controller-layer fallback

**[src/Controllers/CreditosController.php:66](src/Controllers/CreditosController.php#L66)**:
```php
'aplica_mora' => Request::post('aplica_mora', 0),
```

### 3.4 Form

**[views/vendedor/credito_form.php:177-202](views/vendedor/credito_form.php#L177-L202)**:
- Línea 177: `x-data="{ aplica: true }"` → `x-data="{ aplica: false }"`
- Línea 180-182: quitar el atributo `checked` del `<input type="checkbox" name="aplica_mora">`.

Resultado: nuevo crédito → checkbox apagado → `aplica_mora = 0` → cron de mora ignora el crédito (filtro `cr.aplica_mora = 1` en [src/Services/MoraService.php:43-50](src/Services/MoraService.php#L43-L50) se cumple correctamente).

### 3.5 Nada más a tocar en `MoraService` ni `PagoService`

El cron de [cron/devengar_mora.php](cron/devengar_mora.php) seguirá ejecutándose, pero ningún crédito calificará para devengamiento. Si en el futuro alguien activa el flag manualmente para un crédito puntual, el sistema lo respetará.

---

## Cambio 4 — Historial de rendiciones para el cobrador

El admin ya tiene historial completo en [views/admin/rendiciones.php](views/admin/rendiciones.php) y [rendicion_detalle.php](views/admin/rendicion_detalle.php). El gap es que el cobrador no puede ver más que la rendición del día.

### 4.1 Modelo

**[src/Models/Rendicion.php](src/Models/Rendicion.php)** — agregar método:
```php
public function getDelCobrador(int $cobradorId, int $limit = 90): array
{
    return $this->query(
        "SELECT r.*, s.nombre AS sucursal_nombre,
                (SELECT COUNT(*) FROM pagos p WHERE p.rendicion_id = r.id) AS total_pagos
         FROM rendiciones r
         JOIN sucursales s ON r.sucursal_id = s.id
         WHERE r.cobrador_id = ?
         ORDER BY r.fecha DESC, r.id DESC
         LIMIT ?",
        [$cobradorId, $limit]
    )->fetchAll();
}

public function getDelCobradorConPagos(int $rendicionId, int $cobradorId): ?array
{
    // mismo SELECT que Rendicion::getConPagos() pero con WHERE cobrador_id adicional
    // (filtro de seguridad: que un cobrador no vea rendiciones ajenas).
}
```

### 4.2 Rutas

En **[public/index.php](public/index.php)** (sección COBRADOR, después de línea 99):
```php
$router->get('/cobrador/rendiciones',       [\App\Controllers\CobradorController::class, 'rendiciones'],       $auth);
$router->get('/cobrador/rendiciones/{id}',  [\App\Controllers\CobradorController::class, 'rendicionDetalle'], $auth);
```

### 4.3 Controlador

**[src/Controllers/CobradorController.php](src/Controllers/CobradorController.php)** — agregar:
```php
public function rendiciones(): void
{
    $this->requireStaff();
    $rendiciones = (new Rendicion())->getDelCobrador(Auth::id());
    $this->view('cobrador/rendiciones', compact('rendiciones'));
}

public function rendicionDetalle(array $params): void
{
    $this->requireStaff();
    $rendicion = (new Rendicion())->getDelCobradorConPagos((int)$params['id'], Auth::id());
    if (!$rendicion) Response::abort(404, 'Rendición no encontrada.');
    $this->view('cobrador/rendicion_detalle', compact('rendicion'));
}
```

### 4.4 Vistas

- **`views/cobrador/rendiciones.php`** (nueva): listado al estilo de `views/cobrador/historial.php` — header con totales (total declarado, total confirmado, pendientes), agrupado por mes, badges por estado (`pendiente` ámbar, `confirmada` esmeralda, `rechazada` rojo). Cada item linkea a `/cobrador/rendiciones/{id}`.
- **`views/cobrador/rendicion_detalle.php`** (nueva): reusa la estructura de [views/admin/rendicion_detalle.php](views/admin/rendicion_detalle.php) pero **solo lectura** — sin formularios de confirmar/rechazar. Muestra cabecera (fecha, sucursal, montos, estado, observaciones del admin si fue rechazada) + desglose de pagos (cliente, cuota, método, monto, mora).

### 4.5 Ajuste a `cierre_caja.php`

Agregar al final un link "Ver historial de rendiciones" que apunte a `/cobrador/rendiciones`, para que la entrada al historial sea descubrible desde el flujo de cierre de caja.

---

## Archivos a tocar — resumen

**Nuevos**:
- `migrations/005_metodo_pago_y_mora_default.sql`
- `views/cobrador/rendiciones.php`
- `views/cobrador/rendicion_detalle.php`

**Modificados**:
- `migrations/001_schema.sql` (defaults para instalaciones limpias)
- `src/Core/Auth.php` (constante STAFF + `isStaff()`)
- `src/Core/Controller.php` (`requireStaff()`)
- `src/Controllers/PagosController.php` (requireStaff, metodo_pago, fix bug recibo)
- `src/Controllers/CobradorController.php` (requireStaff + 2 métodos rendiciones)
- `src/Controllers/CreditosController.php` (requireStaff donde falta, default aplica_mora=0, layout staff)
- `src/Controllers/ClientesController.php` (requireStaff donde falta)
- `src/Controllers/ReportesController.php` (queries cobradores → IN)
- `src/Models/Usuario.php` (getCobradores → IN)
- `src/Models/Rendicion.php` (`getDelCobrador`, `getDelCobradorConPagos`)
- `src/Services/CreditoService.php` (default aplica_mora=0)
- `src/Services/PagoService.php` (firma + INSERT con metodo_pago)
- `views/layouts/partials/sidebar.php` (nav unificado)
- `views/vendedor/credito_form.php` (checkbox mora desactivado)
- `views/cobrador/pago_form.php` (selector metodo_pago)
- `views/cobrador/recibo_pdf.php` (línea metodo_pago)
- `views/cobrador/cierre_caja.php` (link al historial)
- `views/cobrador/historial.php` (badge método, opcional)
- `views/admin/rendicion_detalle.php` (columna método)
- `views/shared/credito_detalle.php` (Auth::isStaff)
- `public/index.php` (2 rutas nuevas)

---

## Verification

Tras ejecutar la migración (`mysql -u root a0040079_credinor < migrations/005_metodo_pago_y_mora_default.sql`):

1. **Roles unificados**:
   - Login como vendedor → sidebar muestra Agenda, Caja, Rendiciones, Historial además de Clientes y Créditos.
   - Login como cobrador → sidebar muestra Clientes y Créditos además de los items operativos.
   - Como vendedor, abrir `/cobrador/agenda` → entra (antes redirigía).
   - Como cobrador, abrir `/vendedor/creditos/nuevo` → puede dar de alta un crédito.
   - Como admin, asignar cliente a un usuario → el dropdown lista cobradores **y** vendedores activos.

2. **Método de pago**:
   - Registrar pago en efectivo → verificar `SELECT id, metodo_pago FROM pagos ORDER BY id DESC LIMIT 1` devuelve `'efectivo'`.
   - Registrar pago en transferencia → idem `'transferencia'`.
   - Recibo PDF muestra "Método: Efectivo/Transferencia".
   - Detalle de rendición admin muestra columna método con badges.

3. **Mora desactivada por defecto**:
   - Crear nuevo crédito → checkbox de mora aparece **apagado**.
   - `SELECT aplica_mora FROM creditos ORDER BY id DESC LIMIT 1` → `0`.
   - `SELECT COUNT(*) FROM creditos WHERE aplica_mora = 1` → `0` (todos los existentes).
   - Ejecutar `php cron/devengar_mora.php` manualmente → el log debe reportar 0 cuotas devengadas.

4. **Historial de rendiciones cobrador**:
   - Como cobrador con rendiciones pasadas → `/cobrador/rendiciones` lista todas, ordenadas por fecha desc.
   - Click en una rendición → muestra desglose de pagos sin botones de confirmar/rechazar.
   - Como cobrador A intentar abrir `/cobrador/rendiciones/{id_de_B}` → 404 (filtro `cobrador_id = Auth::id()`).
   - Verificar que el cierre de caja muestre el link al historial.

5. **Smoke admin**: dashboard, reportes (cartera, mora, cobradores), confirmación/rechazo de rendiciones — sin regresiones.
