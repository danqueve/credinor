<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #1e293b; line-height: 1.6; }

  .cover { text-align: center; padding: 80px 40px; border-bottom: 3px solid #2563eb; margin-bottom: 0; }
  .cover-logo { font-size: 36px; font-weight: 900; color: #2563eb; letter-spacing: -1px; }
  .cover-sub  { font-size: 14px; color: #64748b; margin-top: 6px; }
  .cover-title { font-size: 22px; font-weight: 700; color: #0f172a; margin-top: 40px; }
  .cover-date  { font-size: 10px; color: #94a3b8; margin-top: 10px; }

  h1 { font-size: 20px; font-weight: 800; color: #1e40af; border-bottom: 2px solid #bfdbfe; padding-bottom: 6px; margin: 28px 0 14px; }
  h2 { font-size: 14px; font-weight: 700; color: #1d4ed8; margin: 20px 0 8px; }
  h3 { font-size: 12px; font-weight: 700; color: #334155; margin: 14px 0 6px; }
  p  { margin-bottom: 8px; }
  ul, ol { margin: 6px 0 10px 20px; }
  li { margin-bottom: 4px; }
  strong { color: #0f172a; }

  .role-header { background: #1d4ed8; color: white; padding: 10px 16px; border-radius: 6px; margin: 30px 0 16px; font-size: 15px; font-weight: 800; letter-spacing: 0.5px; page-break-before: always; }
  .role-header:first-of-type { page-break-before: avoid; }

  table { width: 100%; border-collapse: collapse; margin: 10px 0 16px; font-size: 10px; }
  th { background: #1d4ed8; color: white; padding: 6px 8px; text-align: left; }
  td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; }
  tr:nth-child(even) td { background: #f8fafc; }

  .tip  { background: #eff6ff; border-left: 3px solid #2563eb; padding: 8px 12px; margin: 10px 0; border-radius: 0 4px 4px 0; font-size: 10px; color: #1e40af; }
  .warn { background: #fff7ed; border-left: 3px solid #f97316; padding: 8px 12px; margin: 10px 0; border-radius: 0 4px 4px 0; font-size: 10px; color: #9a3412; }

  .flow { background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 14px 18px; margin: 12px 0; font-size: 10px; color: #334155; }
  .flow .step { display: block; padding: 3px 0; }
  .flow .arrow { color: #2563eb; font-weight: bold; }

  .faq-q { font-weight: 700; color: #1d4ed8; margin-top: 12px; }
  .faq-a { margin-left: 12px; color: #475569; }

  .page-footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 9px; color: #94a3b8; padding: 6px; border-top: 1px solid #e2e8f0; }
</style>
</head>
<body>

<!-- PORTADA -->
<div class="cover">
  <div class="cover-logo">Crédinor</div>
  <div class="cover-sub">Sistema de Gestión de Préstamos</div>
  <div class="cover-title">Manual de Usuario</div>
  <div class="cover-date">Versión 1.0 — <?= date('F Y') ?></div>
</div>

<!-- ACCESO -->
<h1>Acceso al Sistema</h1>
<p>Ingresá desde el navegador a la dirección del sistema. Vas a ver la pantalla de inicio de sesión.</p>
<ul>
  <li><strong>Usuario:</strong> el nombre de usuario que te asignó el administrador</li>
  <li><strong>Contraseña:</strong> la clave que te asignaron</li>
</ul>
<p>Cada perfil tiene acceso solo a las funciones que le corresponden. Al ingresar, el sistema te lleva automáticamente a tu panel.</p>

<div class="tip">El sistema tiene tres perfiles: <strong>Administrador</strong>, <strong>Vendedor</strong> y <strong>Cobrador</strong>. Cada uno ve solo lo que necesita.</div>

<!-- ======== ADMIN ======== -->
<div class="role-header">PERFIL: ADMINISTRADOR</div>

<h2>Dashboard (Panel Principal)</h2>
<p>Al ingresar, el panel muestra un resumen en tiempo real:</p>
<table>
  <tr><th>Indicador</th><th>Qué significa</th></tr>
  <tr><td>Pendientes autorización</td><td>Solicitudes de crédito que esperan tu aprobación</td></tr>
  <tr><td>Créditos activos</td><td>Préstamos en curso</td></tr>
  <tr><td>Mora pendiente</td><td>Total de mora acumulada sin cobrar</td></tr>
  <tr><td>Rendiciones pendientes</td><td>Cierres de caja del cobrador que esperan confirmación</td></tr>
  <tr><td>Cobrado hoy</td><td>Dinero cobrado en el día</td></tr>
  <tr><td>Cuotas vencen hoy</td><td>Cuotas que vencen este día</td></tr>
</table>

<h2>Créditos</h2>
<p>En <strong>Menú → Créditos</strong> ves el listado completo. Podés filtrar por estado: pendiente autorización, activo, finalizado, rechazado o cancelado.</p>

<h3>Autorizar un crédito</h3>
<ol>
  <li>Entrá a <strong>Créditos</strong> → filtrá por "Pendiente autorización"</li>
  <li>Hacé clic en el crédito para ver los detalles</li>
  <li>Revisá monto prestado, monto a devolver, cuotas y frecuencia</li>
  <li>Clic en <strong>Autorizar</strong> → seleccioná el cobrador asignado</li>
  <li>El sistema genera todas las cuotas automáticamente</li>
</ol>

<h3>Rechazar un crédito</h3>
<ol>
  <li>En la pantalla de autorización → clic en <strong>Rechazar</strong></li>
  <li>Ingresá el motivo del rechazo</li>
  <li>El vendedor verá el crédito como rechazado con el motivo indicado</li>
</ol>

<h2>Rendiciones</h2>
<p>Cuando un cobrador cierra su caja al final del día, genera una rendición que vos debés confirmar.</p>

<h3>Confirmar una rendición</h3>
<ol>
  <li>Entrá a <strong>Rendiciones</strong> → vas a ver las rendiciones en estado "Pendiente"</li>
  <li>Clic en la rendición para ver el detalle de todos los cobros del día</li>
  <li>Verificá que el monto declarado coincida con el dinero que tenés en mano</li>
  <li>Ingresá el <strong>monto recibido</strong> real y hacé clic en <strong>Confirmar</strong></li>
</ol>
<div class="warn">Si hay diferencia, el sistema la registra automáticamente. Los pagos pasan de "pendiente rendir" a "confirmado".</div>

<h2>Usuarios</h2>
<p>En <strong>Menú → Usuarios</strong> gestionás el personal del sistema.</p>
<h3>Crear un usuario</h3>
<ol>
  <li>Clic en <strong>+ Nuevo usuario</strong></li>
  <li>Completá nombre, usuario, contraseña (mínimo 6 caracteres), rol y sucursal</li>
  <li>Clic en <strong>Crear usuario</strong></li>
</ol>
<p>Para <strong>cambiar contraseña</strong>: editá el usuario y escribí la nueva clave. Si dejás el campo vacío, la contraseña no cambia.</p>
<p>Para <strong>desactivar</strong>: clic en <strong>Desactivar</strong>. El usuario no puede ingresar pero no se elimina. Se puede reactivar después.</p>

<h2>Sucursales</h2>
<p>En <strong>Menú → Sucursales</strong> podés crear, editar y activar/desactivar sucursales. Una sucursal inactiva no aparece disponible al crear usuarios.</p>

<h2>Reportes</h2>
<table>
  <tr><th>Reporte</th><th>Qué muestra</th></tr>
  <tr><td>Cartera activa</td><td>Créditos activos con filtros por sucursal, cobrador, frecuencia y fechas. Exportable a CSV.</td></tr>
  <tr><td>Mora</td><td>Clientes con mora ordenados de mayor a menor deuda, con resumen por cobrador.</td></tr>
  <tr><td>Cobradores</td><td>Rendimiento por cobrador: pagos, total cobrado, rendiciones y diferencias. Filtro por período.</td></tr>
</table>

<!-- ======== VENDEDOR ======== -->
<div class="role-header">PERFIL: VENDEDOR</div>

<h2>Dashboard</h2>
<p>Al ingresar, el panel muestra mis créditos pendientes de autorización y mis créditos activos. Accesos rápidos para crear cliente nuevo o solicitud de crédito.</p>

<h2>Clientes</h2>
<h3>Crear un cliente nuevo</h3>
<ol>
  <li>Clic en <strong>+ Nuevo cliente</strong></li>
  <li>Completá los datos:
    <ul>
      <li><strong>DNI</strong> (obligatorio, único en el sistema)</li>
      <li><strong>Nombre completo</strong> (obligatorio)</li>
      <li><strong>Teléfono:</strong> para que el cobrador pueda contactarlo</li>
      <li><strong>Domicilio y localidad:</strong> para que el cobrador sepa dónde ir</li>
      <li><strong>Ubicación en mapa:</strong> marcá la ubicación exacta para el cobrador</li>
    </ul>
  </li>
  <li>Clic en <strong>Crear cliente</strong></li>
</ol>

<h2>Mis Créditos</h2>
<h3>Crear una solicitud de crédito</h3>
<ol>
  <li>Clic en <strong>+ Nueva solicitud de crédito</strong></li>
  <li>Buscá el cliente por nombre o DNI</li>
  <li>Completá las condiciones del préstamo:</li>
</ol>
<table>
  <tr><th>Campo</th><th>Descripción</th></tr>
  <tr><td>Monto prestado</td><td>Dinero que se le entrega al cliente</td></tr>
  <tr><td>Monto a devolver</td><td>Total que va a pagar el cliente (≥ monto prestado)</td></tr>
  <tr><td>Cantidad de cuotas</td><td>Número de pagos</td></tr>
  <tr><td>Frecuencia</td><td>Diaria / Semanal / Quincenal / Mensual</td></tr>
  <tr><td>Fecha de inicio</td><td>Cuándo se entrega el dinero</td></tr>
  <tr><td>Fecha primera cuota</td><td>Cuándo vence el primer pago</td></tr>
  <tr><td>¿Aplica mora?</td><td>Si el sistema debe calcular mora por cuotas vencidas</td></tr>
  <tr><td>% mora diaria</td><td>Porcentaje personalizado (opcional, se usa el global si se deja vacío)</td></tr>
  <tr><td>Garante</td><td>Cliente garante del préstamo (opcional)</td></tr>
  <tr><td>Observaciones</td><td>Notas internas</td></tr>
</table>
<div class="warn">Una vez enviada la solicitud no podés modificarla. Si hay un error, avisale al administrador para que la rechace y creás una nueva.</div>

<!-- ======== COBRADOR ======== -->
<div class="role-header">PERFIL: COBRADOR</div>

<h2>Agenda (Pantalla Principal)</h2>
<p>Al ingresar, el cobrador va directamente a su agenda del día. Tiene tres pestañas:</p>
<table>
  <tr><th>Pestaña</th><th>Qué muestra</th></tr>
  <tr><td><strong>Hoy</strong></td><td>Clientes con cuota que vence hoy, ordenados por nombre</td></tr>
  <tr><td><strong>Vencidas</strong></td><td>Clientes con cuotas de días anteriores sin pagar (borde rojo, muestra días de atraso)</td></tr>
  <tr><td><strong>Próximas</strong></td><td>Vista previa de los cobros de los próximos 7 días</td></tr>
</table>

<p>Cada tarjeta de cliente tiene:</p>
<ul>
  <li>Nombre, domicilio, monto de la cuota y mora acumulada</li>
  <li>Botón <strong>Cobrar</strong> → registrar el pago</li>
  <li>Botón WhatsApp → abre chat directo con el cliente</li>
  <li>Botón Mapa → abre Google Maps con la ubicación del cliente</li>
</ul>

<h2>Registrar un Pago</h2>
<ol>
  <li>En la agenda, tocá <strong>Cobrar</strong> en la tarjeta del cliente</li>
  <li>Ves la ficha del crédito con el detalle de todas las cuotas</li>
  <li>En el formulario: ingresá el <strong>monto</strong> que te entrega el cliente</li>
  <li>Si querés imputar parte a mora, ingresá el monto en <strong>Monto a mora</strong> (opcional)</li>
  <li>Tocá <strong>Registrar pago</strong></li>
</ol>
<p>El sistema actualiza el estado de la cuota automáticamente:</p>
<ul>
  <li>Monto completo → cuota <strong>Pagada</strong></li>
  <li>Monto parcial → cuota <strong>Parcial</strong> (el saldo sigue pendiente)</li>
</ul>
<div class="tip">Después del pago podés generar e imprimir el <strong>recibo en PDF</strong> con el botón "Ver recibo".</div>

<h2>Cerrar Caja</h2>
<p>Al final del día, antes de entregar el dinero al administrador:</p>
<ol>
  <li>Entrá a <strong>Cerrar caja</strong></li>
  <li>Revisá el listado de todos los cobros del día con el total</li>
  <li>Si todo es correcto, tocá <strong>Cerrar caja del día</strong></li>
  <li>Se genera una <strong>rendición</strong> que el administrador recibirá para confirmar</li>
</ol>
<div class="warn">Solo podés cerrar caja una vez por día. Si olvidás cerrarla, los pagos quedan pendientes y podés cerrar al día siguiente incluyendo todos los pagos anteriores.</div>

<h2>Historial</h2>
<p>En <strong>Menú → Historial</strong> ves todos los cobros de los últimos 60 días, agrupados por fecha. Desde acá podés acceder al recibo de cada pago.</p>

<!-- ======== FLUJO ======== -->
<div class="role-header" style="page-break-before: always;">FLUJO COMPLETO DE UN PRÉSTAMO</div>

<div class="flow">
  <span class="step">1. <strong>VENDEDOR</strong> crea el cliente (si no existe)</span>
  <span class="step arrow">↓</span>
  <span class="step">2. <strong>VENDEDOR</strong> carga la solicitud de crédito</span>
  <span class="step arrow">↓</span>
  <span class="step">3. <strong>ADMIN</strong> revisa y autoriza → asigna cobrador → el sistema genera todas las cuotas automáticamente</span>
  <span class="step arrow">↓</span>
  <span class="step">4. <strong>COBRADOR</strong> ve al cliente en su agenda según la frecuencia del crédito</span>
  <span class="step arrow">↓</span>
  <span class="step">5. <strong>COBRADOR</strong> registra el pago cuando cobra</span>
  <span class="step arrow">↓</span>
  <span class="step">6. <strong>COBRADOR</strong> cierra caja al final del día</span>
  <span class="step arrow">↓</span>
  <span class="step">7. <strong>ADMIN</strong> confirma la rendición con el dinero recibido</span>
  <span class="step arrow">↓</span>
  <span class="step">8. Cuando se pagan todas las cuotas y la mora → el crédito pasa a <strong>FINALIZADO</strong> automáticamente</span>
</div>

<!-- ======== FAQ ======== -->
<h1>Preguntas Frecuentes</h1>

<p class="faq-q">¿Qué pasa si el cliente paga más de lo que debe en la cuota?</p>
<p class="faq-a">El excedente no se imputa automáticamente a la siguiente cuota. Registrá el monto real cobrado y aclaralo en observaciones.</p>

<p class="faq-q">¿Se puede pagar en cuotas parciales?</p>
<p class="faq-a">Sí. La cuota queda en estado "Parcial" y el saldo restante sigue pendiente. Podés registrar múltiples pagos sobre la misma cuota.</p>

<p class="faq-q">¿Cómo funciona la mora?</p>
<p class="faq-a">El sistema calcula mora automáticamente cada noche sobre las cuotas vencidas con saldo pendiente. El porcentaje es configurable globalmente o por crédito. Al registrar un pago podés indicar cuánto del monto va a mora y cuánto a capital.</p>

<p class="faq-q">¿Qué pasa si me olvido de cerrar caja?</p>
<p class="faq-a">Los pagos quedan en estado "pendiente rendir". Podés cerrar caja al día siguiente e incluye todos los pagos pendientes de días anteriores.</p>

<p class="faq-q">¿Cómo imprimo el recibo?</p>
<p class="faq-a">Después de registrar un pago, el cobrador tiene la opción "Ver recibo". Genera un PDF optimizado para impresoras térmicas de 80mm que podés imprimir desde el celular.</p>

<p class="faq-q">¿Puedo cambiar el cobrador de un crédito activo?</p>
<p class="faq-a">Sí, el administrador puede hacerlo desde la pantalla de edición del crédito en <strong>Menú → Créditos → Editar</strong>.</p>

<div class="page-footer">Crédinor — Manual de Usuario — <?= date('Y') ?></div>

</body>
</html>
