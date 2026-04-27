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
    
    $isVencida = $tipo === 'vencida';
    $bgClass = $isVencida ? 'border-red-500' : 'border-brand-500';
    $iconColor = $isVencida ? 'text-red-500' : 'text-brand-500';
    $iconBg = $isVencida ? 'bg-red-50' : 'bg-brand-50';
    $iconClass = $isVencida ? 'isax-danger' : 'isax-task-square';
    
    $tel      = preg_replace('/\D/', '', $cu['telefono'] ?? '');
    $waLink   = $tel ? "https://wa.me/549{$tel}" : null;
    $pagoUrl  = url('cobrador/pago/' . $cu['credito_id'] . '/' . $cu['id']);
    $inicial  = mb_strtoupper(mb_substr($cu['cliente_nombre'], 0, 1));

    ob_start();
    ?>
    <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden group mb-3">
        <div class="absolute top-0 left-0 w-1.5 h-full <?= $bgClass ?>"></div>
        
        <div class="flex items-start gap-3 mb-3 pl-2">
            <div class="w-10 h-10 rounded-full <?= $iconBg ?> <?= $iconColor ?> flex items-center justify-center font-bold text-sm shrink-0 border border-slate-100/50">
                <?= htmlspecialchars($inicial) ?>
            </div>
            
            <div class="flex-1 min-w-0">
                <div class="flex justify-between items-start gap-2">
                    <h3 class="font-bold text-slate-800 truncate text-base leading-tight"><?= htmlspecialchars($cu['cliente_nombre']) ?></h3>
                    <?php if ($diasAtr > 0): ?>
                        <span class="shrink-0 inline-flex items-center gap-1 text-[10px] font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-md border border-red-100">
                            <i class="isax isax-timer-1 text-red-500"></i> <?= $diasAtr ?>d atraso
                        </span>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($cu['domicilio'])): ?>
                    <p class="text-[11px] font-medium text-slate-500 flex items-center gap-1 mt-1 truncate">
                        <i class="isax isax-location text-slate-400"></i> <?= htmlspecialchars($cu['domicilio']) ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-slate-50 rounded-2xl p-3 border border-slate-100 ml-2">
            <div class="flex items-center justify-between">
                <div>
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Saldo Cuota</span>
                    <span class="block font-extrabold text-slate-800 text-lg leading-none" style="font-family: 'Outfit', sans-serif;">
                        <?= MoneyHelper::formatShort($saldo) ?>
                    </span>
                </div>
                
                <?php if ($mora > 0): ?>
                <div class="text-right">
                    <span class="block text-[10px] font-bold text-red-400 uppercase tracking-wider mb-0.5">Mora</span>
                    <span class="block font-extrabold text-red-600 text-lg leading-none" style="font-family: 'Outfit', sans-serif;">
                        <?= MoneyHelper::formatShort($mora) ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-3 flex gap-2 ml-2">
            <a href="<?= $pagoUrl ?>" class="btn-primary flex-1 justify-center py-2.5 text-sm shadow-md shadow-brand-500/20 active:scale-95">
                <i class="isax isax-wallet-add mr-1"></i> Cobrar
            </a>
            
            <?php if ($waLink): ?>
            <a href="<?= $waLink ?>" target="_blank"
               class="w-11 h-11 shrink-0 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center border border-emerald-100 active:scale-95 transition-transform" title="WhatsApp">
                <i class="isax isax-whatsapp text-xl"></i>
            </a>
            <?php endif; ?>
            
            <?php if (!empty($cu['lat']) && !empty($cu['lng'])): ?>
            <a href="https://maps.google.com/?q=<?= $cu['lat'] ?>,<?= $cu['lng'] ?>"
               target="_blank"
               class="w-11 h-11 shrink-0 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center border border-blue-100 active:scale-95 transition-transform" title="Mapa">
                <i class="isax isax-map-1 text-xl"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
?>
<div class="space-y-6 pb-24 max-w-lg mx-auto" x-data="{ tab: 'hoy' }">

    <!-- Header con totales App-like -->
    <div class="bg-gradient-to-br from-brand-600 to-brand-800 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 p-6 opacity-10 pointer-events-none">
            <i class="isax isax-calendar-1 text-8xl"></i>
        </div>
        
        <div class="relative z-10">
            <div class="flex items-center gap-1.5 text-brand-200 text-xs font-bold uppercase tracking-wider mb-1">
                <i class="isax isax-calendar-tick"></i> <?= date('l d \d\e F', time()) ?>
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight" style="font-family: 'Outfit', sans-serif;">Agenda</h1>
            
            <div class="mt-5 grid grid-cols-2 gap-4">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10">
                    <p class="text-brand-200 text-[10px] font-bold uppercase tracking-wider mb-1">Cobrado Hoy</p>
                    <p class="text-2xl font-extrabold" style="font-family: 'Outfit', sans-serif;"><?= MoneyHelper::formatShort($totalCobrado) ?></p>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10">
                    <p class="text-brand-200 text-[10px] font-bold uppercase tracking-wider mb-1">Pendientes</p>
                    <p class="text-2xl font-extrabold" style="font-family: 'Outfit', sans-serif;"><?= $totalClientes ?> <span class="text-sm font-medium text-brand-200 font-normal">clientes</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs estilo iOS -->
    <div class="bg-slate-100/80 p-1.5 rounded-2xl flex gap-1 shadow-inner border border-slate-200/50">
        <button @click="tab = 'hoy'"
                :class="tab === 'hoy' ? 'bg-white shadow-sm text-brand-700 font-bold border-slate-200' : 'text-slate-500 font-medium hover:bg-slate-200/50'"
                class="flex-1 py-2.5 text-sm rounded-xl transition-all border border-transparent flex items-center justify-center gap-1.5">
            Hoy
            <?php if (!empty($hoy)): ?>
                <span :class="tab === 'hoy' ? 'bg-brand-100 text-brand-700' : 'bg-slate-200 text-slate-600'" class="rounded-full px-1.5 py-0.5 text-[10px] font-bold transition-colors">
                    <?= count($hoy) ?>
                </span>
            <?php endif; ?>
        </button>
        <button @click="tab = 'vencida'"
                :class="tab === 'vencida' ? 'bg-white shadow-sm text-red-600 font-bold border-slate-200' : 'text-slate-500 font-medium hover:bg-slate-200/50'"
                class="flex-1 py-2.5 text-sm rounded-xl transition-all border border-transparent flex items-center justify-center gap-1.5">
            Vencidas
            <?php if (!empty($vencida)): ?>
                <span :class="tab === 'vencida' ? 'bg-red-100 text-red-600' : 'bg-red-50 text-red-500'" class="rounded-full px-1.5 py-0.5 text-[10px] font-bold transition-colors">
                    <?= count($vencida) ?>
                </span>
            <?php endif; ?>
        </button>
        <button @click="tab = 'proximas'"
                :class="tab === 'proximas' ? 'bg-white shadow-sm text-slate-800 font-bold border-slate-200' : 'text-slate-500 font-medium hover:bg-slate-200/50'"
                class="flex-1 py-2.5 text-sm rounded-xl transition-all border border-transparent flex items-center justify-center">
            Próximas
        </button>
    </div>

    <!-- TAB HOY -->
    <div x-show="tab === 'hoy'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
        <?php if (empty($hoy)): ?>
            <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center py-12 px-6">
                <div class="w-20 h-20 rounded-full bg-brand-50 text-brand-500 flex items-center justify-center mx-auto mb-4">
                    <i class="isax isax-task-square text-4xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-1">¡Agenda al día!</h3>
                <p class="text-sm text-slate-500 font-medium">No hay cobros agendados para hoy.</p>
            </div>
        <?php else: ?>
            <div>
                <?php foreach ($hoy as $cu): ?>
                    <?= renderCuotaCard($cu, 'hoy') ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- TAB VENCIDAS -->
    <div x-show="tab === 'vencida'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
        <?php if (empty($vencida)): ?>
            <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center py-12 px-6">
                <div class="w-20 h-20 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center mx-auto mb-4">
                    <i class="isax isax-verify text-4xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-1">Excelente trabajo</h3>
                <p class="text-sm text-slate-500 font-medium">No hay cuotas vencidas en la cartera.</p>
            </div>
        <?php else: ?>
            <div>
                <?php foreach ($vencida as $cu): ?>
                    <?= renderCuotaCard($cu, 'vencida') ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- TAB PRÓXIMAS -->
    <div x-show="tab === 'proximas'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
        <?php if (empty($futura)): ?>
            <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center py-12 px-6">
                <div class="w-20 h-20 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-4">
                    <i class="isax isax-calendar-search text-4xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-1">Próxima semana libre</h3>
                <p class="text-sm text-slate-500 font-medium">No hay cobros en los próximos 7 días.</p>
            </div>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($futura as $cu): ?>
                    <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] flex items-center gap-4">
                        <div class="w-14 h-14 bg-slate-50 rounded-2xl flex flex-col items-center justify-center text-center shrink-0 border border-slate-100">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider leading-none mb-1"><?= date('M', strtotime($cu['fecha_vencimiento'])) ?></span>
                            <span class="font-extrabold text-slate-800 text-lg leading-none" style="font-family: 'Outfit', sans-serif;"><?= date('d', strtotime($cu['fecha_vencimiento'])) ?></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800 truncate text-base mb-0.5"><?= e($cu['cliente_nombre']) ?></p>
                            <?php if (!empty($cu['domicilio'])): ?>
                                <p class="text-[11px] font-medium text-slate-500 flex items-center gap-1 truncate">
                                    <i class="isax isax-location text-slate-400"></i> <?= e($cu['domicilio']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="shrink-0 text-right">
                            <span class="block text-[10px] font-bold text-brand-600 uppercase mb-0.5">Cuota #<?= $cu['numero_cuota'] ?></span>
                            <span class="font-extrabold text-slate-800" style="font-family: 'Outfit', sans-serif;">
                                <?= MoneyHelper::formatShort((float)$cu['monto']) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Botón flotante cierre de caja (Mobile) -->
    <div class="fixed bottom-6 left-0 right-0 px-4 z-50 md:relative md:bottom-auto md:px-0">
        <div class="max-w-lg mx-auto">
            <a href="<?= url('cobrador/caja') ?>"
               class="bg-slate-900 text-white w-full rounded-2xl py-4 px-6 flex items-center justify-between shadow-xl shadow-slate-900/20 border border-slate-700 active:scale-[0.98] transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                        <i class="isax isax-briefcase text-xl text-white"></i>
                    </div>
                    <span class="font-bold text-base">Cierre de Caja Diario</span>
                </div>
                <i class="isax isax-arrow-right-3 opacity-50"></i>
            </a>
        </div>
    </div>
</div>

<script>
    // Inicializar Alpine si no está cargado globalmente (como backup)
    document.addEventListener('alpine:init', () => {
        // Alpine detectado y listo
    });
</script>
