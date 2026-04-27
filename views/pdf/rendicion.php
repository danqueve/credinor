<?php
use App\Helpers\MoneyHelper;

$declarado = (float)$rendicion['monto_declarado'];
$recibido  = (float)($rendicion['monto_recibido'] ?? 0);
$diff      = $recibido > 0 ? $recibido - $declarado : null;

$estadoLabel = [
    'pendiente'  => 'PENDIENTE',
    'confirmada' => 'CONFIRMADA',
    'rechazada'  => 'RECHAZADA',
][$rendicion['estado']] ?? strtoupper($rendicion['estado']);

$estadoColor = [
    'pendiente'  => '#d97706',
    'confirmada' => '#059669',
    'rechazada'  => '#dc2626',
][$rendicion['estado']] ?? '#64748b';
?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #1e293b; background: #fff; }
  .page { padding: 32px 36px; }

  /* Encabezado */
  .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #4f46e5; padding-bottom: 16px; margin-bottom: 20px; }
  .brand { font-size: 22px; font-weight: 900; color: #4f46e5; letter-spacing: -0.5px; }
  .brand-sub { font-size: 10px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }
  .doc-title { text-align: right; }
  .doc-title h1 { font-size: 16px; font-weight: 800; color: #0f172a; }
  .doc-title .rendicion-id { font-size: 13px; color: #4f46e5; font-weight: 700; margin-top: 2px; }
  .doc-title .fecha { font-size: 10px; color: #64748b; margin-top: 4px; }

  /* Badge estado */
  .estado-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #fff; margin-top: 6px; }

  /* Info general */
  .info-grid { display: flex; gap: 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; margin-bottom: 18px; }
  .info-item { flex: 1; }
  .info-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8; margin-bottom: 3px; }
  .info-value { font-size: 12px; font-weight: 700; color: #0f172a; }

  /* Totales */
  .totales-grid { display: flex; gap: 12px; margin-bottom: 18px; }
  .total-card { flex: 1; border-radius: 8px; padding: 12px 14px; }
  .total-card.dark { background: #0f172a; color: #fff; }
  .total-card.light { background: #f8fafc; border: 1px solid #e2e8f0; }
  .total-card.green { background: #ecfdf5; border: 1px solid #bbf7d0; }
  .total-card.purple { background: #f5f3ff; border: 1px solid #ddd6fe; }
  .total-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 5px; }
  .total-card.dark .total-label { color: #94a3b8; }
  .total-card.light .total-label { color: #64748b; }
  .total-card.green .total-label { color: #059669; }
  .total-card.purple .total-label { color: #7c3aed; }
  .total-amount { font-size: 16px; font-weight: 900; }
  .total-card.dark .total-amount { color: #fff; }
  .total-card.light .total-amount { color: #0f172a; }
  .total-card.green .total-amount { color: #059669; }
  .total-card.purple .total-amount { color: #7c3aed; }

  /* Tabla */
  table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
  thead th { background: #1e293b; color: #fff; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 8px 10px; text-align: left; }
  thead th.right { text-align: right; }
  thead th.center { text-align: center; }
  tbody tr:nth-child(even) { background: #f8fafc; }
  tbody td { padding: 7px 10px; border-bottom: 1px solid #f1f5f9; font-size: 10px; color: #334155; vertical-align: middle; }
  tbody td.right { text-align: right; }
  tbody td.center { text-align: center; }
  tbody td.bold { font-weight: 700; color: #0f172a; }
  tbody td.red { color: #dc2626; }
  tbody td.brand { color: #4f46e5; font-weight: 700; }
  tfoot td { padding: 9px 10px; font-weight: 800; font-size: 11px; background: #0f172a; color: #fff; }
  tfoot td.right { text-align: right; font-size: 13px; }

  /* Badges */
  .badge-ef { display: inline-block; background: #d1fae5; color: #065f46; padding: 2px 7px; border-radius: 20px; font-size: 9px; font-weight: 800; text-transform: uppercase; }
  .badge-tr { display: inline-block; background: #ede9fe; color: #5b21b6; padding: 2px 7px; border-radius: 20px; font-size: 9px; font-weight: 800; text-transform: uppercase; }

  /* Diferencia */
  .diff-ok { color: #059669; }
  .diff-neg { color: #dc2626; }
  .diff-pos { color: #2563eb; }

  /* Footer */
  .footer { border-top: 1px solid #e2e8f0; padding-top: 12px; margin-top: 10px; display: flex; justify-content: space-between; font-size: 9px; color: #94a3b8; }
</style>
</head>
<body>
<div class="page">

  <!-- Encabezado -->
  <div class="header">
    <div>
      <div class="brand">Crédinor</div>
      <div class="brand-sub">Sistema de Gestión de Préstamos</div>
    </div>
    <div class="doc-title">
      <h1>Cierre de Caja — Rendición</h1>
      <div class="rendicion-id">#<?= $rendicion['id'] ?></div>
      <div class="fecha">Generado el <?= date('d/m/Y H:i') ?></div>
      <div>
        <span class="estado-badge" style="background:<?= $estadoColor ?>;"><?= $estadoLabel ?></span>
      </div>
    </div>
  </div>

  <!-- Info general -->
  <div class="info-grid">
    <div class="info-item">
      <div class="info-label">Cobrador</div>
      <div class="info-value"><?= htmlspecialchars($rendicion['cobrador_nombre']) ?></div>
    </div>
    <div class="info-item">
      <div class="info-label">Sucursal</div>
      <div class="info-value"><?= htmlspecialchars($rendicion['sucursal_nombre']) ?></div>
    </div>
    <div class="info-item">
      <div class="info-label">Fecha del Cierre</div>
      <div class="info-value"><?= date('d/m/Y', strtotime($rendicion['fecha'])) ?></div>
    </div>
    <div class="info-item">
      <div class="info-label">Cobros Incluidos</div>
      <div class="info-value"><?= count($rendicion['pagos']) ?> recibo<?= count($rendicion['pagos']) !== 1 ? 's' : '' ?></div>
    </div>
  </div>

  <!-- Totales por método + total declarado -->
  <div class="totales-grid">
    <div class="total-card green">
      <div class="total-label">Efectivo</div>
      <div class="total-amount"><?= MoneyHelper::format($totalEfectivo) ?></div>
    </div>
    <div class="total-card purple">
      <div class="total-label">Transferencia</div>
      <div class="total-amount"><?= MoneyHelper::format($totalTransferencia) ?></div>
    </div>
    <div class="total-card dark">
      <div class="total-label">Total Declarado</div>
      <div class="total-amount"><?= MoneyHelper::format($declarado) ?></div>
    </div>
    <?php if ($recibido > 0): ?>
    <div class="total-card light">
      <div class="total-label">Físicamente Recibido</div>
      <div class="total-amount <?= $diff === null ? '' : ($diff < -0.01 ? 'diff-neg' : ($diff > 0.01 ? 'diff-pos' : 'diff-ok')) ?>">
        <?= MoneyHelper::format($recibido) ?>
      </div>
      <?php if ($diff !== null && abs($diff) > 0.01): ?>
      <div style="font-size:9px; margin-top:3px; color:<?= $diff < 0 ? '#dc2626' : '#2563eb' ?>; font-weight:700;">
        Diferencia: <?= $diff > 0 ? '+' : '' ?><?= MoneyHelper::format($diff) ?>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- Detalle de pagos -->
  <table>
    <thead>
      <tr>
        <th style="width:30%">Cliente</th>
        <th style="width:8%" class="center">Cuota</th>
        <th style="width:10%" class="center">Método</th>
        <th style="width:17%" class="right">Capital</th>
        <th style="width:15%" class="right">Mora</th>
        <th style="width:20%" class="right">Total Cobrado</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rendicion['pagos'] as $p): ?>
      <tr>
        <td class="bold"><?= htmlspecialchars($p['cliente_nombre']) ?></td>
        <td class="center">#<?= $p['numero_cuota'] ?></td>
        <td class="center">
          <?php if ($p['metodo_pago'] === 'transferencia'): ?>
            <span class="badge-tr">Transf.</span>
          <?php else: ?>
            <span class="badge-ef">Efvo.</span>
          <?php endif; ?>
        </td>
        <td class="right"><?= MoneyHelper::format((float)$p['monto_a_capital']) ?></td>
        <td class="right red"><?= (float)$p['monto_a_mora'] > 0 ? MoneyHelper::format((float)$p['monto_a_mora']) : '—' ?></td>
        <td class="right brand"><?= MoneyHelper::format((float)$p['monto']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="5" style="text-align:right; color:#94a3b8; font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Total Rendido</td>
        <td class="right"><?= MoneyHelper::format($declarado) ?></td>
      </tr>
    </tfoot>
  </table>

  <!-- Observaciones -->
  <?php if (!empty($rendicion['observaciones'])): ?>
  <div style="background:#fef9c3; border:1px solid #fde047; border-radius:6px; padding:10px 14px; margin-bottom:14px; font-size:10px; color:#713f12;">
    <strong>Observaciones:</strong> <?= htmlspecialchars($rendicion['observaciones']) ?>
  </div>
  <?php endif; ?>

  <!-- Footer -->
  <div class="footer">
    <span>Crédinor · Sistema de Gestión de Préstamos</span>
    <span>Rendición #<?= $rendicion['id'] ?> · <?= date('d/m/Y H:i') ?></span>
  </div>

</div>
</body>
</html>
