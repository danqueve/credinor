<?php $title = 'Manual de Usuario'; ?>

<!-- PORTADA -->
<div class="cover">
    <img src="<?= asset('img/logo.png') ?>" alt="Crédinor" style="height:64px;object-fit:contain;margin-bottom:12px;">
    <div class="cover-title">Manual de Usuario</div>
    <div class="cover-sub">Sistema de Gestión de Préstamos y Cobranza</div>
    <div class="cover-badge">Versión 1.0 — <?= date('Y') ?></div>
</div>

<!-- ACCESO -->
<div class="section">
    <h2>Acceso al Sistema</h2>
    <p>Ingresá desde el navegador a la dirección del sistema. Vas a ver la pantalla de inicio de sesión.</p>
    <ul>
        <li><strong>Usuario:</strong> el nombre de usuario que te asignó el administrador</li>
        <li><strong>Contraseña:</strong> la clave que te asignaron</li>
    </ul>
    <p>Cada perfil tiene acceso solo a las funciones que le corresponden. Al ingresar, el sistema te lleva automáticamente a tu panel.</p>
    <div class="tip">El sistema tiene tres perfiles: <strong>Administrador</strong>, <strong>Vendedor</strong> y <strong>Cobrador</strong>. Cada uno ve solo lo que necesita para su rol.</div>
</div>

<!-- ======== ADMIN ======== -->
<div class="role-header">👤 PERFIL: ADMINISTRADOR</div>

<div class="section">
    <h2>Dashboard — Panel Principal</h2>
    <p>Al ingresar, el panel muestra un resumen en tiempo real con los indicadores más importantes del negocio:</p>
    <table>
        <thead><tr><th>Indicador</th><th>Qué significa</th></tr></thead>
        <tbody>
            <tr><td><strong>Cobrado hoy</strong></td><td>Dinero cobrado en el día actual</td></tr>
            <tr><td><strong>Cuotas vencen hoy</strong></td><td>Cantidad de cuotas con vencimiento en el día</td></tr>
            <tr><td><strong>Créditos activos</strong></td><td>Total de préstamos vigentes en el sistema</td></tr>
            <tr><td><strong>Mora pendiente</strong></td><td>Total de mora acumulada sin cobrar</td></tr>
            <tr><td><strong>Rendiciones pendientes</strong></td><td>Cierres de caja de cobradores que esperan tu confirmación</td></tr>
        </tbody>
    </table>
    <p>El dashboard también muestra gráficos de cobranza de los últimos 30 días, mora por sucursal y el top 10 de deudores.</p>
</div>

<div class="section">
    <h2>Créditos</h2>
    <p>En <strong>Menú → Créditos</strong> ves el listado completo de todos los créditos del sistema. Podés filtrar por frecuencia y buscar por nombre o DNI del cliente.</p>
    <p>Los estados posibles de un crédito son:</p>
    <table>
        <thead><tr><th>Estado</th><th>Qué significa</th></tr></thead>
        <tbody>
            <tr><td><strong>Activo</strong></td><td>Préstamo vigente en cobro</td></tr>
            <tr><td><strong>Finalizado</strong></td><td>Todas las cuotas y mora fueron pagadas (transición automática)</td></tr>
            <tr><td><strong>Cancelado</strong></td><td>Crédito cancelado manualmente por el administrador</td></tr>
        </tbody>
    </table>

    <h3>Editar un crédito</h3>
    <ol>
        <li>Desde el listado de créditos → clic en <strong>Editar</strong></li>
        <li>Podés modificar: cobrador asignado, montos, cuotas, frecuencia, fechas y observaciones</li>
        <li>Al guardar, el sistema regenera automáticamente todas las cuotas</li>
    </ol>
    <div class="warn">Solo podés editar un crédito que no tenga pagos registrados. Si ya tiene pagos, el sistema no permite modificar los montos.</div>

    <h3>Cancelar un crédito</h3>
    <ol>
        <li>Desde el detalle del crédito → clic en <strong>Cancelar crédito</strong></li>
        <li>Ingresá el motivo de la cancelación</li>
        <li>El crédito y todas sus cuotas pasan a estado cancelado</li>
    </ol>
    <div class="warn">Solo se puede cancelar un crédito que no tenga pagos registrados.</div>

    <h3>Registrar un pago (desde admin)</h3>
    <ol>
        <li>Desde el detalle del crédito → clic en <strong>Registrar pago</strong> en la cuota correspondiente</li>
        <li>Ingresá el monto, el monto a mora (opcional) y el método de pago</li>
        <li>Los pagos registrados por el admin quedan confirmados directamente, sin pasar por rendición</li>
    </ol>

    <h3>Anular un pago</h3>
    <ol>
        <li>En <strong>Menú → Pagos</strong> → buscá el pago a anular</li>
        <li>Clic en <strong>Anular</strong> e ingresá el motivo</li>
        <li>El sistema revierte el estado de la cuota y la mora imputada automáticamente</li>
    </ol>
    <div class="warn">No se puede anular un pago si la rendición del cobrador ya fue confirmada.</div>
</div>

<div class="section">
    <h2>Rendiciones</h2>
    <p>Cuando un cobrador cierra su caja al final del día, genera una rendición que vos debés confirmar.</p>

    <h3>Confirmar una rendición</h3>
    <ol>
        <li>Entrá a <strong>Menú → Rendiciones</strong> → vas a ver las rendiciones en estado "Pendiente"</li>
        <li>Clic en la rendición para ver el detalle de todos los cobros del día, desglosados por método de pago</li>
        <li>Verificá que el monto declarado coincida con el dinero que recibís</li>
        <li>Ingresá el <strong>monto recibido</strong> real y hacé clic en <strong>Confirmar rendición</strong></li>
    </ol>
    <div class="tip">Si hay diferencia entre lo declarado y lo recibido, el sistema la registra automáticamente. Podés verla en el detalle de la rendición.</div>

    <h3>Rechazar una rendición</h3>
    <ol>
        <li>En el detalle de la rendición → clic en <strong>Rechazar</strong></li>
        <li>Ingresá el motivo del rechazo</li>
        <li>El cobrador podrá volver a cerrar caja con los pagos pendientes</li>
    </ol>
</div>

<div class="section">
    <h2>Pagos</h2>
    <p>En <strong>Menú → Pagos</strong> ves el listado completo de todos los pagos del sistema con detalles de cliente, monto, método y estado.</p>
</div>

<div class="section">
    <h2>Usuarios</h2>
    <p>En <strong>Menú → Usuarios</strong> gestionás el personal del sistema.</p>

    <h3>Crear un usuario</h3>
    <ol>
        <li>Clic en <strong>+ Nuevo usuario</strong></li>
        <li>Completá los datos:
            <ul>
                <li><strong>Nombre completo</strong></li>
                <li><strong>Usuario:</strong> nombre con el que va a ingresar (ej: <em>jperez</em>, <em>cobrador1</em>)</li>
                <li><strong>Contraseña</strong> (mínimo 6 caracteres)</li>
                <li><strong>Rol:</strong> Administrador, Vendedor o Cobrador</li>
                <li><strong>Sucursal</strong> a la que pertenece</li>
            </ul>
        </li>
        <li>Clic en <strong>Crear usuario</strong></li>
    </ol>
    <div class="tip">Para cambiar la contraseña de un usuario: editalo y escribí la nueva clave. Si dejás el campo vacío, la contraseña no cambia.</div>
    <p>Para <strong>desactivar</strong> un usuario hacé clic en <strong>Desactivar</strong>. No se elimina y se puede reactivar en cualquier momento.</p>
</div>

<div class="section">
    <h2>Sucursales</h2>
    <p>En <strong>Menú → Sucursales</strong> podés crear, editar y activar/desactivar sucursales.</p>
    <h3>Crear una sucursal</h3>
    <ol>
        <li>Clic en <strong>+ Nueva sucursal</strong></li>
        <li>Ingresá nombre, dirección y teléfono</li>
        <li>Clic en <strong>Crear sucursal</strong></li>
    </ol>
    <div class="warn">Una sucursal inactiva no aparece disponible al crear usuarios ni al filtrar reportes.</div>
</div>

<div class="section">
    <h2>Reportes</h2>
    <table>
        <thead><tr><th>Reporte</th><th>Qué muestra</th><th>Filtros disponibles</th></tr></thead>
        <tbody>
            <tr>
                <td><strong>Cartera activa</strong></td>
                <td>Todos los créditos activos con montos, saldos y mora. Exportable a CSV para Excel.</td>
                <td>Sucursal, cobrador, frecuencia, rango de fechas</td>
            </tr>
            <tr>
                <td><strong>Mora</strong></td>
                <td>Ranking de los 50 clientes con mayor mora pendiente y resumen por cobrador</td>
                <td>—</td>
            </tr>
            <tr>
                <td><strong>Cobradores</strong></td>
                <td>Performance de cada cobrador: pagos, total cobrado, rendiciones y diferencias. Incluye gráfico de tendencia de los últimos 30 días.</td>
                <td>Período: hoy / semana / mes</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- ======== VENDEDOR ======== -->
<div class="role-header">🛒 PERFIL: VENDEDOR</div>

<div class="section">
    <h2>Dashboard</h2>
    <p>Al ingresar, el panel muestra tus indicadores personales: créditos activos, cobrado hoy y cuotas pendientes. Tiene accesos rápidos para crear un cliente nuevo o una solicitud de crédito.</p>
</div>

<div class="section">
    <h2>Clientes</h2>
    <p>En <strong>Menú → Clientes</strong> ves todos los clientes. Podés buscar por nombre o DNI.</p>

    <h3>Crear un cliente nuevo</h3>
    <ol>
        <li>Clic en <strong>+ Nuevo cliente</strong></li>
        <li>Completá los datos:
            <ul>
                <li><strong>DNI</strong> (obligatorio, único en el sistema)</li>
                <li><strong>Nombre completo</strong> (obligatorio)</li>
                <li><strong>Teléfono:</strong> para que el cobrador pueda contactarlo por WhatsApp</li>
                <li><strong>Domicilio y localidad:</strong> dirección donde vive el cliente</li>
                <li><strong>Ubicación en mapa:</strong> marcá el punto exacto en el mapa para que el cobrador sepa dónde ir desde su celular</li>
                <li><strong>Observaciones:</strong> notas internas sobre el cliente</li>
            </ul>
        </li>
        <li>Clic en <strong>Crear cliente</strong></li>
    </ol>

    <h3>Ver ficha de un cliente</h3>
    <p>Desde el listado → clic en <strong>Ver</strong>. Ves los datos del cliente y todos sus créditos asociados con su estado.</p>
</div>

<div class="section">
    <h2>Mis Créditos</h2>
    <p>En <strong>Menú → Mis Créditos</strong> ves todos los créditos de tu sucursal.</p>

    <h3>Crear un crédito</h3>
    <ol>
        <li>Clic en <strong>+ Nuevo crédito</strong></li>
        <li>Buscá el cliente por nombre o DNI en el buscador</li>
        <li>Completá las condiciones del préstamo:</li>
    </ol>
    <table>
        <thead><tr><th>Campo</th><th>Descripción</th></tr></thead>
        <tbody>
            <tr><td><strong>Monto prestado</strong></td><td>Dinero que se le entrega al cliente</td></tr>
            <tr><td><strong>Monto a devolver</strong></td><td>Total que va a pagar el cliente (debe ser ≥ al monto prestado)</td></tr>
            <tr><td><strong>Cantidad de cuotas</strong></td><td>Número de pagos (entre 1 y 60)</td></tr>
            <tr><td><strong>Frecuencia</strong></td><td>Diaria / Semanal / Quincenal / Mensual</td></tr>
            <tr><td><strong>Fecha de inicio</strong></td><td>Cuándo se entrega el dinero al cliente</td></tr>
            <tr><td><strong>Fecha primera cuota</strong></td><td>Cuándo vence el primer pago</td></tr>
            <tr><td><strong>¿Aplica mora?</strong></td><td>Si el sistema debe calcular mora por cuotas vencidas automáticamente</td></tr>
            <tr><td><strong>% mora diaria</strong></td><td>Porcentaje personalizado (si se deja vacío se usa el % global del sistema)</td></tr>
            <tr><td><strong>Observaciones</strong></td><td>Notas internas del crédito</td></tr>
        </tbody>
    </table>
    <ol start="4">
        <li>Clic en <strong>Crear crédito</strong></li>
        <li>El crédito queda <strong>activo inmediatamente</strong> y el sistema genera todas las cuotas con sus fechas de vencimiento</li>
    </ol>
    <div class="tip">El monto de cada cuota se calcula dividiendo el monto a devolver entre la cantidad de cuotas. La última cuota absorbe la diferencia de centavos si la hubiera.</div>
</div>

<!-- ======== COBRADOR ======== -->
<div class="role-header">💼 PERFIL: COBRADOR</div>

<div class="section">
    <h2>Dashboard</h2>
    <p>Al ingresar, el panel muestra tus indicadores del día: cuotas a cobrar hoy, total cobrado y acceso rápido a tu agenda.</p>
</div>

<div class="section">
    <h2>Agenda — Pantalla Principal</h2>
    <p>En <strong>Menú → Agenda</strong> tenés tu lista de cobros organizada en tres pestañas:</p>
    <table>
        <thead><tr><th>Pestaña</th><th>Qué muestra</th></tr></thead>
        <tbody>
            <tr><td><strong>Hoy</strong></td><td>Clientes con cuota que vence hoy. Ordenados por nombre.</td></tr>
            <tr><td><strong>Vencidas</strong></td><td>Cuotas de días anteriores sin pagar (borde rojo, muestra los días de atraso).</td></tr>
            <tr><td><strong>Próximas</strong></td><td>Vista previa de los cobros de los próximos 7 días para planificar la semana.</td></tr>
        </tbody>
    </table>

    <p>Cada tarjeta de cliente muestra el nombre, domicilio, monto de la cuota y mora acumulada. También tiene botones de acceso rápido:</p>
    <ul>
        <li><strong>Cobrar:</strong> abre el formulario de pago</li>
        <li><strong>WhatsApp:</strong> abre un chat directo con el número del cliente</li>
        <li><strong>Mapa:</strong> abre Google Maps con la ubicación exacta del cliente</li>
    </ul>
</div>

<div class="section">
    <h2>Registrar un Pago</h2>
    <ol>
        <li>Desde la agenda, tocá <strong>Cobrar</strong> en la tarjeta del cliente</li>
        <li>Ves la ficha del crédito con el detalle de todas las cuotas (pagadas y pendientes)</li>
        <li>En el formulario de pago completá:
            <ul>
                <li><strong>Monto:</strong> el dinero que te entrega el cliente</li>
                <li><strong>Monto a mora</strong> (opcional): si querés imputar parte del pago a la mora. Por defecto todo el pago va a capital.</li>
                <li><strong>Método de pago:</strong> Efectivo o Transferencia</li>
            </ul>
        </li>
        <li>Tocá <strong>Registrar pago</strong></li>
    </ol>

    <p>El sistema actualiza el estado de la cuota automáticamente:</p>
    <ul>
        <li>El monto cubre el saldo completo → cuota <strong>Pagada</strong></li>
        <li>El monto es parcial → cuota <strong>Parcial</strong> (el saldo sigue pendiente)</li>
    </ul>
    <div class="tip">Después de registrar el pago podés tocar <strong>"Guardar y ver recibo"</strong> para generar un PDF de recibo optimizado para impresoras térmicas de 80mm.</div>
    <div class="warn">Los pagos quedan en estado "pendiente de rendir" hasta que cerrés caja. El administrador los confirma cuando confirmás la rendición.</div>
</div>

<div class="section">
    <h2>Cerrar Caja</h2>
    <p>Al final del día, antes de entregar el dinero al administrador:</p>
    <ol>
        <li>Entrá a <strong>Menú → Cerrar Caja</strong></li>
        <li>Ves el listado de todos los cobros del día con el total por método de pago (efectivo y transferencia por separado)</li>
        <li>Si el listado es correcto, tocá <strong>Cerrar caja del día</strong></li>
        <li>Se genera una <strong>rendición</strong> que el administrador recibirá para confirmar</li>
    </ol>
    <div class="warn">Solo podés cerrar caja una vez por día. Si necesitás corregir algo, avisale al administrador para que rechace la rendición y así podés volver a cerrar.</div>
    <div class="tip">Si olvidaste cerrar caja ayer, los pagos quedan en "pendiente de rendir". Podés cerrar caja hoy y el sistema incluye automáticamente todos los pagos pendientes de días anteriores.</div>
</div>

<div class="section">
    <h2>Historial</h2>
    <p>En <strong>Menú → Historial</strong> ves todos los cobros que registraste en los últimos 60 días, agrupados por fecha. Desde acá también podés acceder al recibo de cada pago.</p>
</div>

<div class="section">
    <h2>Mis Rendiciones</h2>
    <p>En <strong>Menú → Mis Rendiciones</strong> ves el historial de todos tus cierres de caja y su estado (pendiente, confirmada o rechazada).</p>
</div>

<!-- ======== FLUJO ======== -->
<div class="role-header">🔄 FLUJO COMPLETO DE UN PRÉSTAMO</div>

<div class="section">
    <div class="flow">
        <div class="flow-step"><div class="num">1</div><div class="text"><strong>VENDEDOR</strong> registra al cliente en el sistema (si no existe)</div></div>
        <div class="flow-arrow">↓</div>
        <div class="flow-step"><div class="num">2</div><div class="text"><strong>VENDEDOR</strong> crea el crédito con las condiciones acordadas — queda <strong>activo inmediatamente</strong></div></div>
        <div class="flow-arrow">↓</div>
        <div class="flow-step"><div class="num">3</div><div class="text">El sistema genera todas las cuotas automáticamente con sus fechas de vencimiento según la frecuencia elegida</div></div>
        <div class="flow-arrow">↓</div>
        <div class="flow-step"><div class="num">4</div><div class="text"><strong>COBRADOR</strong> ve al cliente en su agenda cada vez que vence una cuota</div></div>
        <div class="flow-arrow">↓</div>
        <div class="flow-step"><div class="num">5</div><div class="text"><strong>COBRADOR</strong> registra el pago al cobrar — el pago queda en estado "pendiente de rendir"</div></div>
        <div class="flow-arrow">↓</div>
        <div class="flow-step"><div class="num">6</div><div class="text"><strong>COBRADOR</strong> cierra caja al final del día — se genera la rendición con el total del día</div></div>
        <div class="flow-arrow">↓</div>
        <div class="flow-step"><div class="num">7</div><div class="text"><strong>ADMIN</strong> confirma la rendición ingresando el monto físico recibido</div></div>
        <div class="flow-arrow">↓</div>
        <div class="flow-step"><div class="num">8</div><div class="text">Cuando se pagan todas las cuotas y la mora → el crédito pasa a <strong>FINALIZADO</strong> automáticamente</div></div>
    </div>
</div>

<!-- ======== FAQ ======== -->
<div class="role-header">❓ PREGUNTAS FRECUENTES</div>

<div class="section">
    <div class="faq-item">
        <div class="faq-q">¿Los créditos necesitan aprobación del administrador?</div>
        <div class="faq-a">No. Los créditos creados por el vendedor o cobrador quedan activos inmediatamente. El sistema genera las cuotas de forma automática.</div>
    </div>
    <div class="faq-item">
        <div class="faq-q">¿Qué pasa si el cliente paga más de lo que debe en la cuota?</div>
        <div class="faq-a">El excedente no se imputa automáticamente a la siguiente cuota. Registrá el monto real cobrado y usá el campo de observaciones para aclararlo.</div>
    </div>
    <div class="faq-item">
        <div class="faq-q">¿Se puede pagar una cuota en forma parcial?</div>
        <div class="faq-a">Sí. La cuota queda en estado "Parcial" y el saldo restante sigue pendiente. Podés registrar múltiples pagos sobre la misma cuota.</div>
    </div>
    <div class="faq-item">
        <div class="faq-q">¿Cómo funciona la mora?</div>
        <div class="faq-a">Si el crédito tiene mora activada, el sistema la calcula automáticamente sobre las cuotas vencidas con saldo pendiente. Al registrar un pago podés indicar cuánto del monto va a mora y cuánto a capital.</div>
    </div>
    <div class="faq-item">
        <div class="faq-q">¿Puedo registrar un pago en transferencia bancaria?</div>
        <div class="faq-a">Sí. En el formulario de pago seleccionás "Transferencia" como método. La rendición al cierre de caja muestra el detalle separado por efectivo y transferencia.</div>
    </div>
    <div class="faq-item">
        <div class="faq-q">¿Qué pasa si me olvido de cerrar caja?</div>
        <div class="faq-a">Los pagos quedan en estado "pendiente de rendir". Podés cerrar caja al día siguiente y el sistema los incluye automáticamente.</div>
    </div>
    <div class="faq-item">
        <div class="faq-q">¿Cómo imprimo el recibo de un pago?</div>
        <div class="faq-a">Después de registrar el pago usá el botón <strong>"Guardar y ver recibo"</strong>, o desde el historial entrá al pago y tocá "Ver recibo". Genera un PDF para impresora térmica de 80mm.</div>
    </div>
    <div class="faq-item">
        <div class="faq-q">¿Un cliente puede tener más de un crédito activo?</div>
        <div class="faq-a">Sí. Un mismo cliente puede tener múltiples créditos activos de forma simultánea.</div>
    </div>
    <div class="faq-item">
        <div class="faq-q">¿Cómo exporto los datos a Excel?</div>
        <div class="faq-a">En el reporte de <strong>Cartera activa</strong> (Menú → Reportes → Cartera) hay un botón de descarga CSV que podés abrir directamente en Excel.</div>
    </div>
</div>
