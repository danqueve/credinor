<?php // views/cobrador/recibo_pdf.php
use App\Helpers\MoneyHelper;
use App\Helpers\DateHelper;
// $pago viene del controller
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: Helvetica, Arial, sans-serif;
        font-size: 10px;
        color: #111;
        width: 226px;
        padding: 8px;
    }
    .center { text-align: center; }
    .bold { font-weight: bold; }
    .large { font-size: 14px; }
    .xlarge { font-size: 18px; }
    hr { border: none; border-top: 1px dashed #999; margin: 6px 0; }
    .row { display: flex; justify-content: space-between; margin: 2px 0; }
    .muted { color: #555; }
    .footer { font-size: 8px; color: #888; text-align: center; }
    .logo { font-size: 16px; font-weight: bold; color: #1e40af; letter-spacing: 1px; }
    .highlight { background: #f0f7ff; border-left: 2px solid #1e40af; padding: 4px 6px; margin: 4px 0; }
</style>
</head>
<body>

<div class="center">
    <div class="logo">CRÉDINOR</div>
    <div class="muted" style="font-size:8px">Sistema de Préstamos</div>
</div>

<hr>

<div class="center">
    <div class="bold" style="font-size:11px">RECIBO DE COBRO</div>
    <div class="muted">N° <?= str_pad($pago['id'], 6, '0', STR_PAD_LEFT) ?></div>
</div>

<hr>

<div class="row">
    <span class="muted">Fecha:</span>
    <span><?= date('d/m/Y H:i', strtotime($pago['created_at'])) ?></span>
</div>
<div class="row">
    <span class="muted">Cobrador:</span>
    <span><?= htmlspecialchars($pago['cobrador_nombre']) ?></span>
</div>

<hr>

<div class="bold" style="margin-bottom:3px">CLIENTE</div>
<div><?= htmlspecialchars($pago['cliente_nombre']) ?></div>
<div class="muted">DNI <?= htmlspecialchars($pago['dni']) ?></div>
<?php if ($pago['domicilio']): ?>
<div class="muted"><?= htmlspecialchars($pago['domicilio']) ?></div>
<?php endif; ?>

<hr>

<div class="bold" style="margin-bottom:3px">CRÉDITO</div>
<div class="row">
    <span class="muted">Cuota N°:</span>
    <span><?= $pago['numero_cuota'] ?> / <?= $pago['cantidad_cuotas'] ?></span>
</div>
<div class="row">
    <span class="muted">Vencimiento:</span>
    <span><?= DateHelper::formatoArg($pago['fecha_vencimiento']) ?></span>
</div>
<div class="row">
    <span class="muted">Monto cuota:</span>
    <span><?= MoneyHelper::format((float)$pago['monto_cuota']) ?></span>
</div>

<hr>

<div class="highlight">
    <div class="row">
        <span class="muted">A capital:</span>
        <span><?= MoneyHelper::format((float)$pago['monto_a_capital']) ?></span>
    </div>
    <?php if ((float)$pago['monto_a_mora'] > 0): ?>
    <div class="row">
        <span class="muted">A mora:</span>
        <span><?= MoneyHelper::format((float)$pago['monto_a_mora']) ?></span>
    </div>
    <?php endif; ?>
</div>

<hr>

<div class="center">
    <div class="muted" style="margin-bottom:2px">TOTAL RECIBIDO</div>
    <div class="xlarge bold"><?= MoneyHelper::format((float)$pago['monto']) ?></div>
    <div class="muted" style="margin-top:4px; font-size:9px; text-transform:uppercase;">
        <?= $pago['metodo_pago'] === 'transferencia' ? 'VÍA TRANSFERENCIA' : 'EN EFECTIVO' ?>
    </div>
</div>

<hr>

<div class="footer">
    <div>Este comprobante es válido como recibo de pago.</div>
    <div>Crédinor — <?= date('Y') ?></div>
</div>

</body>
</html>
