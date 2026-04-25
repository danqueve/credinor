<?php // views/cobrador/cierre_caja.php
use App\Helpers\MoneyHelper;
?>
<div class="max-w-lg mx-auto space-y-4">
    <h1 class="text-2xl font-bold">💼 Cierre de caja</h1>

    <?php if ($rendicion): ?>
        <!-- Ya cerró caja hoy -->
        <div class="card bg-green-50 border border-green-200 text-center py-8 space-y-2">
            <div class="text-4xl">✅</div>
            <p class="font-bold text-green-700">Caja cerrada correctamente</p>
            <p class="text-sm text-green-600">
                Total rendido: <strong><?= MoneyHelper::format((float)$rendicion['monto_declarado']) ?></strong>
            </p>
            <p class="text-xs text-green-500">
                Estado: <?= ucfirst($rendicion['estado']) ?>
            </p>
        </div>
    <?php else: ?>
        <!-- Pagos del día -->
        <?php if (empty($pagos)): ?>
            <div class="card text-center py-10 text-gray-400">
                <div class="text-4xl mb-2">📭</div>
                <p>No registraste cobros hoy.</p>
                <a href="<?= url('cobrador/agenda') ?>" class="btn-primary mt-4 inline-block">Ir a la agenda</a>
            </div>
        <?php else: ?>
            <div class="card space-y-3">
                <h2 class="font-semibold text-gray-700">Cobros del día</h2>
                <?php foreach ($pagos as $p): ?>
                <div class="flex justify-between items-center py-2 border-b border-gray-50 last:border-0 text-sm">
                    <div>
                        <p class="font-medium"><?= e($p['cliente_nombre']) ?></p>
                        <p class="text-xs text-gray-400">Cuota #<?= $p['numero_cuota'] ?></p>
                    </div>
                    <span class="font-bold text-brand-700"><?= MoneyHelper::formatShort((float)$p['monto']) ?></span>
                </div>
                <?php endforeach; ?>
                <div class="flex justify-between pt-2 font-bold">
                    <span>TOTAL</span>
                    <span class="text-xl text-brand-700"><?= MoneyHelper::format($total) ?></span>
                </div>
            </div>

            <!-- Formulario de cierre -->
            <div class="card border-brand-200 bg-brand-50">
                <p class="text-sm text-brand-700 mb-4">
                    Al cerrar la caja, el administrador recibirá la rendición con
                    <strong><?= MoneyHelper::format($total) ?></strong> para confirmar.
                </p>
                <form method="POST" action="<?= url('cobrador/caja/cerrar') ?>">
                    <?= csrf_field() ?>
                    <button type="submit"
                            onclick="return confirm('¿Confirmás el cierre de caja por <?= MoneyHelper::formatShort($total) ?>?')"
                            class="btn-primary w-full py-3 font-bold text-base">
                        ✅ Cerrar caja (<?= MoneyHelper::formatShort($total) ?>)
                    </button>
                </form>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <a href="<?= url('cobrador/agenda') ?>"
       class="block text-center text-sm text-gray-400 hover:text-gray-600 py-2">
        ← Volver a la agenda
    </a>
</div>
