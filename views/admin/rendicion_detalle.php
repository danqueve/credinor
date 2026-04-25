<?php // views/admin/rendicion_detalle.php
use App\Helpers\MoneyHelper;

$declarado = (float)$rendicion['monto_declarado'];
$recibido  = (float)($rendicion['monto_recibido'] ?? 0);
$diff      = $recibido > 0 ? $recibido - $declarado : null;
?>
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex items-center gap-3">
        <a href="<?= url('admin/rendiciones') ?>" class="text-gray-400 hover:text-gray-600">←</a>
        <h1 class="text-xl font-bold">Rendición #<?= $rendicion['id'] ?></h1>
        <?php if ($rendicion['estado'] === 'pendiente'): ?>
            <span class="badge-pendiente">Pendiente</span>
        <?php elseif ($rendicion['estado'] === 'confirmada'): ?>
            <span class="badge-activo">Confirmada</span>
        <?php else: ?>
            <span class="badge-rechazado">Rechazada</span>
        <?php endif; ?>
    </div>

    <!-- Info -->
    <div class="card grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
        <div>
            <p class="text-gray-400 text-xs">Cobrador</p>
            <p class="font-semibold"><?= e($rendicion['cobrador_nombre']) ?></p>
        </div>
        <div>
            <p class="text-gray-400 text-xs">Sucursal</p>
            <p class="font-medium"><?= e($rendicion['sucursal_nombre']) ?></p>
        </div>
        <div>
            <p class="text-gray-400 text-xs">Fecha</p>
            <p class="font-medium"><?= date('d/m/Y', strtotime($rendicion['fecha'])) ?></p>
        </div>
        <div>
            <p class="text-gray-400 text-xs">Cobros</p>
            <p class="font-bold text-brand-700"><?= count($rendicion['pagos']) ?></p>
        </div>
    </div>

    <!-- Montos -->
    <div class="card grid grid-cols-3 gap-4 text-center">
        <div>
            <p class="text-xs text-gray-400 mb-1">Declarado</p>
            <p class="text-2xl font-bold text-brand-700"><?= MoneyHelper::format($declarado) ?></p>
        </div>
        <div>
            <p class="text-xs text-gray-400 mb-1">Recibido</p>
            <p class="text-2xl font-bold <?= $recibido > 0 ? 'text-green-700' : 'text-gray-400' ?>">
                <?= $recibido > 0 ? MoneyHelper::format($recibido) : '—' ?>
            </p>
        </div>
        <div>
            <p class="text-xs text-gray-400 mb-1">Diferencia</p>
            <?php if ($diff !== null): ?>
                <p class="text-2xl font-bold <?= abs($diff) < 0.01 ? 'text-green-600' : 'text-red-600' ?>">
                    <?= $diff >= 0 ? '+' : '' ?><?= MoneyHelper::format($diff) ?>
                </p>
            <?php else: ?>
                <p class="text-2xl font-bold text-gray-300">—</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Detalle de pagos -->
    <div class="card">
        <h2 class="font-semibold border-b pb-2 mb-3">Cobros incluidos</h2>
        <div class="table-container">
            <table class="table text-sm">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Cuota</th>
                        <th class="text-right">Capital</th>
                        <th class="text-right">Mora</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rendicion['pagos'] as $p): ?>
                    <tr>
                        <td class="font-medium"><?= e($p['cliente_nombre']) ?></td>
                        <td class="text-gray-500">#<?= $p['numero_cuota'] ?></td>
                        <td class="text-right"><?= MoneyHelper::formatShort((float)$p['monto_a_capital']) ?></td>
                        <td class="text-right text-red-600"><?= MoneyHelper::formatShort((float)$p['monto_a_mora']) ?></td>
                        <td class="text-right font-bold"><?= MoneyHelper::formatShort((float)$p['monto']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="font-semibold text-right">TOTAL</td>
                        <td class="text-right font-bold text-brand-700">
                            <?= MoneyHelper::format($declarado) ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Acciones solo si está pendiente -->
    <?php if ($rendicion['estado'] === 'pendiente'): ?>
    <div class="card space-y-4">
        <h2 class="font-semibold">✅ Confirmar rendición</h2>
        <form method="POST" action="<?= url('admin/rendiciones/' . $rendicion['id'] . '/confirmar') ?>">
            <?= csrf_field() ?>
            <label for="monto_recibido" class="form-label">
                Monto físicamente recibido
                <span class="text-xs text-gray-400 ml-1">(declarado: <?= MoneyHelper::format($declarado) ?>)</span>
            </label>
            <div class="flex gap-3">
                <div class="relative flex-1">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">$</span>
                    <input id="monto_recibido" type="number" name="monto_recibido"
                           step="0.01" min="0" required
                           class="form-input pl-7"
                           value="<?= number_format($declarado, 2, '.', '') ?>">
                </div>
                <button type="submit" class="btn-success">Confirmar</button>
            </div>
        </form>

        <!-- Rechazar -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="text-sm text-red-600 hover:text-red-800">
                ❌ Rechazar rendición
            </button>
            <div x-show="open" x-cloak class="mt-3">
                <form method="POST" action="<?= url('admin/rendiciones/' . $rendicion['id'] . '/rechazar') ?>">
                    <?= csrf_field() ?>
                    <textarea name="motivo" rows="2" required
                              class="form-input resize-none mb-2"
                              placeholder="Motivo del rechazo..."></textarea>
                    <button type="submit" class="btn-danger text-sm">Confirmar rechazo</button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
