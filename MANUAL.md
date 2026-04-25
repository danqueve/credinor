# Manual de Usuario — Crédinor Préstamos

---

## Acceso al sistema

Ingresá desde el navegador a la dirección del sistema. Vas a ver la pantalla de inicio de sesión.

- **Usuario**: el nombre de usuario que te asignó el administrador
- **Contraseña**: la clave que te asignaron (podés pedirle al administrador que la cambie)

Cada perfil tiene acceso solo a las funciones que le corresponden. Al ingresar el sistema te lleva automáticamente a tu panel.

---

## Perfil: ADMINISTRADOR

El administrador tiene control total del sistema. Puede ver y gestionar todo.

---

### Dashboard (panel principal)

Al ingresar, el panel muestra un resumen en tiempo real:

| Indicador | Qué significa |
|-----------|---------------|
| Pendientes autorización | Solicitudes de crédito que esperan tu aprobación |
| Créditos activos | Préstamos en curso |
| Mora pendiente | Total de mora acumulada sin cobrar |
| Rendiciones pendientes | Cierres de caja del cobrador que esperan confirmación |
| Cobrado hoy | Dinero cobrado en el día |
| Cuotas vencen hoy | Cuotas que vencen este día |

Si hay créditos pendientes de autorización, aparece un aviso con acceso directo.

---

### Créditos

**Menú → Créditos**

Ves el listado de todos los créditos del sistema. Podés filtrar por estado:

- **Pendiente autorización**: solicitudes creadas por vendedores que esperan tu aprobación
- **Activo**: préstamos vigentes en cobro
- **Finalizado**: préstamos completamente pagados
- **Rechazado / Cancelado**: solicitudes no aprobadas

#### Autorizar un crédito

1. Entrá a **Créditos** → filtrá por "Pendiente autorización"
2. Hacé clic en el crédito para ver los detalles del cliente y las condiciones
3. Revisá: monto prestado, monto a devolver, cantidad de cuotas, frecuencia
4. Si todo está bien → clic en **Autorizar**
5. Seleccioná el **cobrador** que va a estar a cargo de ese préstamo
6. El sistema genera automáticamente todas las cuotas con sus fechas de vencimiento
7. El crédito pasa a estado **Activo**

#### Rechazar un crédito

1. En la pantalla de autorización → clic en **Rechazar**
2. Ingresá el motivo del rechazo
3. El vendedor verá el crédito como rechazado con el motivo indicado

---

### Clientes

**Menú → Clientes**

Vista de todos los clientes de todas las sucursales. Podés buscar por nombre o DNI.
Desde acá podés ver la ficha completa de cada cliente y sus créditos asociados.

> Los clientes solo los crea el vendedor. El administrador los puede consultar.

---

### Rendiciones

**Menú → Rendiciones**

Cuando un cobrador cierra su caja al final del día, genera una rendición. Vos debés confirmarla.

#### Confirmar una rendición

1. Entrá a **Rendiciones** → vas a ver las rendiciones en estado "Pendiente"
2. Clic en la rendición para ver el detalle de todos los cobros del día
3. Verificá que el monto declarado coincida con el dinero que tenés en mano
4. Ingresá el **monto recibido** real
5. Hacé clic en **Confirmar**
   - Si hay diferencia, el sistema la registra automáticamente
   - Los pagos pasan de "pendiente rendir" a "confirmado"

#### Rechazar una rendición

Si encontrás una diferencia que no podés resolver:
1. Ingresá el motivo del rechazo
2. El cobrador podrá volver a cerrar caja con los pagos pendientes

---

### Usuarios

**Menú → Usuarios**

Gestión completa del personal del sistema.

#### Crear un usuario

1. Clic en **+ Nuevo usuario**
2. Completá:
   - **Nombre completo**: nombre real de la persona
   - **Usuario**: nombre con el que va a ingresar al sistema (ej: `jperez`, `cobrador1`)
   - **Contraseña**: clave de acceso (mínimo 6 caracteres)
   - **Rol**: Vendedor, Cobrador o Administrador
   - **Sucursal**: a cuál sucursal pertenece
3. Clic en **Crear usuario**

#### Editar un usuario

1. Clic en **Editar** junto al usuario
2. Podés cambiar nombre, usuario, rol, sucursal y estado
3. Para cambiar la contraseña, escribí la nueva en el campo correspondiente. Si lo dejás vacío, la contraseña no cambia

#### Activar / desactivar

Clic en **Desactivar** para que el usuario no pueda ingresar al sistema. No se elimina, se puede reactivar después.

---

### Sucursales

**Menú → Sucursales**

Gestión de las sucursales de la empresa.

#### Crear una sucursal

1. Clic en **+ Nueva sucursal**
2. Ingresá nombre, dirección y teléfono
3. Clic en **Crear sucursal**

Podés activar o desactivar sucursales. Una sucursal inactiva no aparece disponible al crear usuarios.

---

### Reportes

**Menú → Reportes**

Tres reportes disponibles:

#### Cartera activa

Muestra todos los créditos en estado activo. Podés filtrar por:
- Sucursal
- Cobrador
- Frecuencia de pago
- Rango de fechas

También podés **exportar a CSV** para abrir en Excel con el botón de descarga.

El resumen muestra: total de créditos, capital prestado, capital a devolver, saldo pendiente y mora.

#### Mora

Lista los clientes con mora acumulada, ordenados de mayor a menor deuda.
También muestra un resumen de mora por cobrador.

#### Cobradores

Estadísticas de rendimiento por cobrador: pagos registrados, total cobrado, rendiciones y diferencias.
Podés filtrar por período: hoy, esta semana o este mes.

---

---

## Perfil: VENDEDOR

El vendedor gestiona clientes y crea solicitudes de crédito.

---

### Dashboard

Al ingresar, el panel muestra:
- Mis créditos pendientes de autorización
- Mis créditos activos

Accesos rápidos para crear un cliente nuevo o una solicitud de crédito.

---

### Clientes

**Menú → Clientes**

#### Buscar un cliente

Podés buscar por nombre o DNI usando el buscador.

#### Crear un cliente nuevo

1. Clic en **+ Nuevo cliente**
2. Completá los datos:
   - **DNI** (obligatorio, único en el sistema)
   - **Nombre completo** (obligatorio)
   - **Teléfono**: para que el cobrador pueda contactarlo y enviar WhatsApp
   - **Domicilio y localidad**: para que el cobrador sepa dónde ir
   - **Ubicación en mapa**: podés marcar la ubicación exacta en el mapa para que el cobrador tenga acceso desde su celular
3. Clic en **Crear cliente**

#### Editar un cliente

Desde la lista o la ficha del cliente → clic en **Editar** → modificá los datos y guardá.

#### Ver ficha de un cliente

Desde la lista → clic en **Ver** → ves los datos del cliente y todos sus créditos asociados.

---

### Mis créditos

**Menú → Mis créditos**

Ves todas las solicitudes de crédito que cargaste, filtradas por estado.

#### Crear una solicitud de crédito

1. Clic en **+ Nueva solicitud de crédito**
2. Buscá el cliente por nombre o DNI en el buscador
3. Completá las condiciones del préstamo:
   - **Monto prestado**: dinero que se le entrega al cliente
   - **Monto a devolver**: total que va a pagar el cliente (debe ser mayor o igual al prestado)
   - **Cantidad de cuotas**: número de pagos
   - **Frecuencia**: diaria / semanal / quincenal / mensual
   - **Fecha de inicio**: cuándo se entrega el dinero
   - **Fecha primera cuota**: cuándo vence el primer pago
   - **¿Aplica mora?**: si el sistema debe calcular mora por cuotas vencidas
   - **% mora diaria**: si querés un porcentaje diferente al global, ingresalo acá (opcional)
   - **Garante**: si el crédito tiene garante, seleccionarlo (opcional)
   - **Observaciones**: notas internas
4. Clic en **Enviar solicitud**

La solicitud queda en estado **Pendiente de autorización**. El administrador la va a revisar y autorizar o rechazar.

> Una vez enviada, no podés modificar la solicitud. Si hay un error, avisale al administrador para que la rechace y creás una nueva.

---

---

## Perfil: COBRADOR

El cobrador trabaja principalmente desde su celular. El sistema está diseñado para ser rápido y simple en pantalla chica.

---

### Agenda (pantalla principal)

Al ingresar, el cobrador va directamente a su **agenda del día**. Esta es la pantalla más importante.

La agenda tiene tres pestañas:

#### Hoy
Clientes que tienen cuota que vence hoy. Ordenados por nombre.

Cada tarjeta muestra:
- Nombre del cliente
- Domicilio
- Monto de la cuota / saldo pendiente
- Mora acumulada (si tiene)
- Botón 💰 **Cobrar** → registrar el pago
- Botón 💬 → abre WhatsApp directo al teléfono del cliente
- Botón 🗺️ → abre Google Maps con la ubicación del cliente

#### Vencidas
Clientes con cuotas vencidas de días anteriores, ordenadas de más antigua a más nueva. Las tarjetas tienen borde rojo y muestran los días de atraso.

#### Próximas
Vista previa de los cobros de los próximos 7 días, para planificar la semana.

---

### Registrar un pago

1. En la agenda, tocá **💰 Cobrar** en la tarjeta del cliente
2. Ves la ficha del crédito con el detalle de todas las cuotas
3. En el formulario de pago:
   - **Monto**: ingresá el dinero que te entrega el cliente
   - **Monto a mora** (opcional): si querés imputar parte del pago a la mora, ingresá cuánto. Por defecto todo va a capital
4. Tocá **Registrar pago**

El sistema actualiza automáticamente el estado de la cuota:
- Si el monto cubre la cuota completa → cuota **Pagada**
- Si el monto es parcial → cuota **Parcial** (el saldo sigue pendiente)

Después del pago podés:
- Volver al dashboard
- Generar e imprimir el **recibo en PDF** (botón "Ver recibo")

---

### Cerrar caja

**Menú → Cerrar caja** (o el botón flotante 💼 desde la agenda)

Al final del día, antes de entregar el dinero al administrador:

1. Entrá a **Cerrar caja**
2. Ves el listado de todos los cobros del día con el total
3. Si el listado es correcto, tocá **Cerrar caja del día**
4. Se genera una **rendición** que el administrador recibirá para confirmar

> Solo podés cerrar caja una vez por día. Si necesitás corregir algo, avisale al administrador para que rechace la rendición y puedas volver a cerrar.

---

### Historial

**Menú → Historial**

Ves todos los cobros que registraste en los últimos 60 días, agrupados por fecha.
Desde acá también podés acceder al recibo de cada pago.

---

---

## Flujo completo de un préstamo

```
1. VENDEDOR crea el cliente (si no existe)
         ↓
2. VENDEDOR carga la solicitud de crédito
         ↓
3. ADMIN revisa y autoriza → asigna cobrador
   El sistema genera todas las cuotas automáticamente
         ↓
4. COBRADOR ve al cliente en su agenda según la frecuencia del crédito
         ↓
5. COBRADOR registra el pago cuando cobra
         ↓
6. COBRADOR cierra caja al final del día
         ↓
7. ADMIN confirma la rendición con el dinero recibido
         ↓
8. Cuando se pagan todas las cuotas y la mora
   → el crédito pasa a FINALIZADO automáticamente
```

---

## Preguntas frecuentes

**¿Qué pasa si el cliente paga más de lo que debe en la cuota?**
El excedente no se imputa automáticamente a la siguiente cuota. Registrá el monto real cobrado y aclaralo en observaciones.

**¿Se puede pagar en cuotas parciales?**
Sí. La cuota queda en estado "Parcial" y el saldo restante sigue pendiente. Podés registrar múltiples pagos sobre la misma cuota.

**¿Cómo funciona la mora?**
El sistema calcula mora automáticamente cada noche sobre las cuotas vencidas con saldo pendiente. El porcentaje es configurable globalmente o por crédito. Al registrar un pago podés indicar cuánto del monto va a mora y cuánto a capital.

**¿Puedo cambiar el cobrador de un crédito activo?**
Actualmente solo el administrador puede hacerlo desde la base de datos. Esta función estará disponible próximamente desde el panel.

**¿Qué pasa si me olvido de cerrar caja?**
Los pagos quedan en estado "pendiente rendir". Podés cerrar caja al día siguiente e incluye todos los pagos pendientes de días anteriores.

**¿Cómo imprimo el recibo?**
Después de registrar un pago, el cobrador tiene la opción "Ver recibo". Esto genera un PDF optimizado para impresoras térmicas de 80mm que podés imprimir desde el celular.
```
