<?php // views/cobrador/agenda.php
use App\Helpers\MoneyHelper;
use App\Helpers\DateHelper;

/**
 * Helper para renderizar la card de una cuota en la agenda.
 * @param array  $cu   Datos de la cuota con joins
 * @param string $tipo 'hoy' | 'vencida'
 */
function renderCuotaCard(array $cu, string $tipo): string
{
    $saldo   = (float)$cu['saldo'];
    $mora    = (float)($cu['mora_acumulada'] ?? 0);
    $diasAtr = 0;
    if ($tipo === 'vencida') {
        $diff    = (new \DateTime())->diff(new \DateTime($cu['fecha_vencimiento']));
        $diasAtr = $diff->invert ? $diff->days : 0;
    }
    $bgClass  = $tipo === 'vencida' ? 'border-l-4 border-red-400' : 'border-l-4 border-brand-400';
    $tel      = preg_replace('/\D/', '', $cu['telefono'] ?? '');
    $waLink   = $tel ? "https://wa.me/549{$tel}" : null;
    $pagoUrl  = url('cobrador/pago/' . $cu['credito_id'] . '/' . $cu['id']);
    $inicial  = mb_strtoupper(mb_substr($cu['cliente_nombre'], 0, 1));

    ob_start();
    ?>
    <div class="card <?= $bgClass ?> hover:shadow-md transition-shadow">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center
                        text-brand-700 font-bold text-sm shrink-0">
                <?= htmlspecialchars($inicial) ?>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex justify-between items-start gap-2">
                    <p class="font-semibold text-gray-900 truncate"><?= htmlspecialchars($cu['cliente_nombre']) ?></p>
                    <?php if ($diasAtr > 0): ?>
                        <span class="badge-vencida shrink-0"><?= $diasAtr ?>d atraso</span>
                    <?php endif; ?>
                </div>
                <p class="text-xs text-gray-400"><?= htmlspecialchars($cu['domicilio'] ?? '—') ?></p>
                <div class="mt-2 flex items-center gap-4">
                    <div>
                        <p class="text-xs text-gray-400">Saldo cuota</p>
                        <p class="font-bold text-gray-800"><?= MoneyHelper::formatShort($saldo) ?></p>
                    </div>
                    <?php if ($mora > 0): ?>
                    <div>
                        <p class="text-xs text-gray-400">Mora</p>
                        <p class="font-bold text-red-600"><?= MoneyHelper::formatShort($mora) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="mt-3 flex gap-2 pt-3 border-t border-gray-100">
            <a href="<?= $pagoUrl ?>" class="btn-primary flex-1 text-center text-sm py-2">
                💰 Cobrar
            </a>
            <?php if ($waLink): ?>
            <a href="<?= $waLink ?>" target="_blank"
               class="btn-secondary px-3 py-2 text-green-600 border-green-200 hover:bg-green-50">
                💬
            </a>
            <?php endif; ?>
            <?php if (!empty($cu['lat']) && !empty($cu['lng'])): ?>
            <a href="https://maps.google.com/?q=<?= $cu['lat'] ?>,<?= $cu['lng'] ?>"
               target="_blank"
               class="btn-secondary px-3 py-2 text-blue-600 border-blue-200 hover:bg-blue-50">
                🗺️
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
?>
<div class="space-y-4 pb-24" x-data="{ tab: 'hoy' }">

    <!-- Header con totales -->
    <div class="bg-gradient-to-br from-brand-600 to-brand-800 rounded-2xl p-4 text-white shadow-lg">
        <p class="text-brand-200 text-xs font-medium uppercase tracking-wider">
            <?= date('l d \d\e F', time()) ?>
        </p>
        <h1 class="text-2xl font-bold mt-0.5">Mi Agenda</h1>
        <div class="mt-3 grid grid-cols-2 gap-3">
            <div class="bg-white/10 rounded-xl p-3">
                <p class="text-brand-200 text-xs">Cobrado hoy</p>
                <p class="text-xl font-bold"><?= MoneyHelper::formatShort($totalCobrado) ?></p>
            </div>
            <div class="bg-white/10 rounded-xl p-3">
                <p class="text-brand-200 text-xs">Clientes pendientes</p>
                <p class="text-xl font-bold"><?= $totalClientes ?></p>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex bg-gray-100 rounded-xl p-1 gap-1">
        <button @click="tab = 'hoy'"
                :class="tab === 'hoy' ? 'bg-white shadow text-brand-700' : 'text-gray-500'"
                class="flex-1 py-2 text-sm font-medium rounded-lg transition-all">
            Hoy
            <?php if (!empty($hoy)): ?>
                <span class="ml-1 bg-brand-100 text-brand-700 rounded-full px-1.5 py-0.5 text-xs">
                    <?= count($hoy) ?>
                </span>
            <?php endif; ?>
        </button>
        <button @click="tab = 'vencida'"
                :class="tab === 'vencida' ? 'bg-white shadow text-red-700' : 'text-gray-500'"
                class="flex-1 py-2 text-sm font-medium rounded-lg transition-all">
            Vencidas
            <?php if (!empty($vencida)): ?>
                <span class="ml-1 bg-red-100 text-red-700 rounded-full px-1.5 py-0.5 text-xs">
                    <?= count($vencida) ?>
                </span>
            <?php endif; ?>
        </button>
        <button @click="tab = 'proximas'"
                :class="tab === 'proximas' ? 'bg-white shadow text-gray-700' : 'text-gray-500'"
                class="flex-1 py-2 text-sm font-medium rounded-lg transition-all">
            Próximas
        </button>
    </div>

    <!-- TAB HOY -->
    <div x-show="tab === 'hoy'" x-cloak>
        <?php if (empty($hoy)): ?>
            <div class="card text-center py-10">
                <div class="text-4xl mb-2">🎉</div>
                <p class="text-gray-500 font-medium">¡Sin cobros pendientes para hoy!</p>
            </div>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($hoy as $cu): ?>
                    <?= renderCuotaCard($cu, 'hoy') ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- TAB VENCIDAS -->
    <div x-show="tab === 'vencida'" x-cloak>
        <?php if (empty($vencida)): ?>
            <div class="card text-center py-10">
                <p class="text-gray-400">Sin cuotas vencidas. 🎉</p>
            </div>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($vencida as $cu): ?>
                    <?= renderCuotaCard($cu, 'vencida') ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- TAB PRÓXIMAS -->
    <div x-show="tab === 'proximas'" x-cloak>
        <?php if (empty($futura)): ?>
            <div class="card text-center py-10">
                <p class="text-gray-400">No hay cobros en los próximos 7 días.</p>
            </div>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($futura as $cu): ?>
                    <div class="card flex items-center gap-3">
                        <div class="w-12 h-12 bg-gray-100 rounded-xl flex flex-col items-center justify-center text-center shrink-0">
                            <span class="text-xs text-gray-500"><?= date('M', strtotime($cu['fecha_vencimiento'])) ?></span>
                            <span class="font-bold text-gray-800"><?= date('d', strtotime($cu['fecha_vencimiento'])) ?></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-800 truncate"><?= e($cu['cliente_nombre']) ?></p>
                            <p class="text-xs text-gray-400"><?= e($cu['domicilio'] ?? '—') ?></p>
                        </div>
                        <span class="text-brand-700 font-bold whitespace-nowrap">
                            <?= MoneyHelper::formatShort((float)$cu['monto']) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Botón flotante cierre de caja -->
    <div class="fixed bottom-6 right-4 z-10">
        <a href="<?= url('cobrador/caja') ?>"
           class="flex items-center gap-2 bg-gray-800 text-white rounded-full px-4 py-3 shadow-xl
                  hover:bg-gray-700 transition-colors font-medium text-sm">
            💼 Cerrar caja
        </a>
    </div>
</div>


