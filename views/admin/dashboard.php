<?php // views/admin/dashboard.php
use App\Helpers\MoneyHelper;

$nombreAdmin = \App\Core\Auth::user()['nombre'] ?? 'Admin';
$deltaTrend  = $tendencia['delta'];
$deltaClass  = $deltaTrend === null ? 'text-slate-400' : ($deltaTrend >= 0 ? 'text-emerald-600' : 'text-red-500');
$deltaIcon   = $deltaTrend === null ? '' : ($deltaTrend >= 0 ? 'isax-arrow-up-3' : 'isax-arrow-down');

// JSON para Chart.js
$cobranzaJson    = json_encode($chartCobranza, JSON_THROW_ON_ERROR);
$moraJson        = json_encode($chartMora, JSON_THROW_ON_ERROR);
$rendicionesJson = json_encode($chartRendiciones, JSON_THROW_ON_ERROR);
?>
<div class="max-w-7xl mx-auto space-y-7 pb-10">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Panel de Administración</p>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family:'Outfit',sans-serif;">
                Hola, <?= e($nombreAdmin) ?>
            </h1>
            <p class="text-sm text-slate-400 mt-1 font-medium">
                <?= date('l d \d\e F Y') ?>
                <span class="text-slate-200 mx-2">·</span>
                <?= e($sucursalNombre) ?>
            </p>
        </div>
        <?php if ($kpis['rendiciones_pendientes'] > 0): ?>
        <a href="<?= url('admin/rendiciones?estado=pendiente') ?>"
           class="inline-flex items-center gap-3 bg-white border border-amber-200 rounded-2xl px-4 py-2.5 shadow-sm hover:shadow-md hover:border-amber-300 transition-all group text-sm font-semibold text-amber-700">
            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
            <?= $kpis['rendiciones_pendientes'] ?> rendición<?= $kpis['rendiciones_pendientes'] !== 1 ? 'es' : '' ?> pendiente<?= $kpis['rendiciones_pendientes'] !== 1 ? 's' : '' ?>
            <i class="isax isax-arrow-right-1 text-amber-400 group-hover:translate-x-1 transition-transform"></i>
        </a>
        <?php endif; ?>
    </div>

    <!-- KPI CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        <!-- Cobrado este mes -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                    <i class="isax isax-wallet-add text-xl"></i>
                </div>
                <?php if ($deltaTrend !== null): ?>
                <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full <?= $deltaTrend >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-500' ?>">
                    <i class="isax <?= $deltaIcon ?>" style="font-size:10px;"></i>
                    <?= abs($deltaTrend) ?>%
                </span>
                <?php endif; ?>
            </div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Cobrado este mes</p>
            <p class="text-2xl font-bold text-slate-900 tracking-tight" style="font-family:'Outfit',sans-serif;">
                <?= MoneyHelper::formatShort($tendencia['mes_actual']) ?>
            </p>
            <p class="text-xs text-slate-400 mt-2 font-medium">
                <span class="text-slate-600 font-semibold"><?= number_format($kpis['pagos_hoy']) ?></span> cobros hoy
            </p>
            <div class="absolute -right-4 -bottom-4 text-[80px] text-brand-600 opacity-[0.035] pointer-events-none group-hover:opacity-[0.06] transition-opacity">
                <i class="isax isax-wallet-add"></i>
            </div>
        </div>

        <!-- Capital activo -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i class="isax isax-bank text-xl"></i>
                </div>
                <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-600">
                    <i class="isax isax-chart-1" style="font-size:10px;"></i> Activo
                </span>
            </div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Capital en cartera</p>
            <p class="text-2xl font-bold text-slate-900 tracking-tight" style="font-family:'Outfit',sans-serif;">
                <?= MoneyHelper::formatShort($kpis['cartera_total']) ?>
            </p>
            <p class="text-xs text-slate-400 mt-2 font-medium">
                <span class="text-slate-600 font-semibold"><?= number_format($kpis['creditos_activos']) ?></span> créditos vigentes
            </p>
            <div class="absolute -right-4 -bottom-4 text-[80px] text-blue-600 opacity-[0.035] pointer-events-none group-hover:opacity-[0.06] transition-opacity">
                <i class="isax isax-bank"></i>
            </div>
        </div>

        <!-- Mora -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <i class="isax isax-graph text-xl"></i>
                </div>
                <?php if ((float)($kpis['mora_pendiente'] ?? 0) > 0): ?>
                <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full bg-red-50 text-red-500">
                    <i class="isax isax-warning-2" style="font-size:10px;"></i> Alerta
                </span>
                <?php else: ?>
                <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600">
                    <i class="isax isax-tick-circle" style="font-size:10px;"></i> OK
                </span>
                <?php endif; ?>
            </div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Mora pendiente</p>
            <p class="text-2xl font-bold <?= (float)($kpis['mora_pendiente'] ?? 0) > 0 ? 'text-rose-600' : 'text-slate-900' ?> tracking-tight" style="font-family:'Outfit',sans-serif;">
                <?= MoneyHelper::formatShort($kpis['mora_pendiente']) ?>
            </p>
            <a href="<?= url('admin/reportes/mora') ?>" class="text-xs text-rose-500 hover:text-rose-700 font-semibold mt-2 inline-flex items-center gap-1 transition-colors">
                Ver detalle <i class="isax isax-arrow-right-1" style="font-size:10px;"></i>
            </a>
            <div class="absolute -right-4 -bottom-4 text-[80px] text-rose-600 opacity-[0.035] pointer-events-none group-hover:opacity-[0.06] transition-opacity">
                <i class="isax isax-graph"></i>
            </div>
        </div>

        <!-- Rendiciones -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl <?= $kpis['rendiciones_pendientes'] > 0 ? 'bg-amber-50 text-amber-600' : 'bg-slate-50 text-slate-400' ?> flex items-center justify-center shrink-0">
                    <i class="isax isax-money-tick text-xl"></i>
                </div>
                <?php if ($kpis['rendiciones_pendientes'] > 0): ?>
                <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-600">
                    <i class="isax isax-clock" style="font-size:10px;"></i> Pendiente
                </span>
                <?php else: ?>
                <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600">
                    <i class="isax isax-tick-circle" style="font-size:10px;"></i> Al día
                </span>
                <?php endif; ?>
            </div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Rendiciones</p>
            <p class="text-2xl font-bold text-slate-900 tracking-tight" style="font-family:'Outfit',sans-serif;">
                <?= number_format($kpis['rendiciones_pendientes']) ?>
                <span class="text-sm font-medium text-slate-400">por confirmar</span>
            </p>
            <?php if ($kpis['rendiciones_pendientes'] > 0): ?>
            <a href="<?= url('admin/rendiciones?estado=pendiente') ?>" class="text-xs text-amber-600 hover:text-amber-700 font-semibold mt-2 inline-flex items-center gap-1 transition-colors">
                Revisar <i class="isax isax-arrow-right-1" style="font-size:10px;"></i>
            </a>
            <?php else: ?>
            <p class="text-xs text-slate-400 mt-2 flex items-center gap-1 font-medium">
                <i class="isax isax-tick-circle text-emerald-400" style="font-size:12px;"></i> Sin pendientes
            </p>
            <?php endif; ?>
            <div class="absolute -right-4 -bottom-4 text-[80px] text-amber-500 opacity-[0.035] pointer-events-none group-hover:opacity-[0.06] transition-opacity">
                <i class="isax isax-money-tick"></i>
            </div>
        </div>
    </div>

    <!-- GRÁFICO COBRANZA 30 DÍAS -->
    <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6">
            <div>
                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="isax isax-chart text-brand-500"></i> Cobranza — últimos 30 días
                </h2>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Monto total cobrado por día</p>
            </div>
        </div>
        <?php if (array_sum(array_column($chartCobranza, 'total')) > 0): ?>
        <div class="h-56">
            <canvas id="chartCobranza"></canvas>
        </div>
        <?php else: ?>
        <div class="h-40 flex flex-col items-center justify-center text-slate-400">
            <i class="isax isax-chart text-4xl mb-2 opacity-30"></i>
            <p class="text-sm font-medium">Sin cobros en los últimos 30 días.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- FILA: MORA POR SUCURSAL + DONUT RENDICIONES -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Mora por sucursal -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-5">
                <i class="isax isax-graph text-rose-500"></i> Mora por sucursal
            </h2>
            <?php if (!empty($chartMora)): ?>
            <div class="h-48">
                <canvas id="chartMora"></canvas>
            </div>
            <?php else: ?>
            <div class="h-32 flex flex-col items-center justify-center text-slate-400">
                <i class="isax isax-tick-circle text-3xl mb-2 text-emerald-400 opacity-60"></i>
                <p class="text-sm font-medium">Sin mora pendiente.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Donut rendiciones -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-5">
                <i class="isax isax-wallet-money text-amber-500"></i> Rendiciones — 30 días
            </h2>
            <?php
            $totalRend = ($chartRendiciones['pendientes'] ?? 0)
                       + ($chartRendiciones['confirmadas'] ?? 0)
                       + ($chartRendiciones['rechazadas'] ?? 0);
            ?>
            <?php if ($totalRend > 0): ?>
            <div class="flex items-center gap-6">
                <div class="w-40 h-40 shrink-0">
                    <canvas id="chartRendiciones"></canvas>
                </div>
                <div class="space-y-3 flex-1">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span> Confirmadas
                        </span>
                        <span class="font-bold text-slate-800"><?= $chartRendiciones['confirmadas'] ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shrink-0"></span> Pendientes
                        </span>
                        <span class="font-bold text-slate-800"><?= $chartRendiciones['pendientes'] ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-sm font-medium text-slate-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0"></span> Rechazadas
                        </span>
                        <span class="font-bold text-slate-800"><?= $chartRendiciones['rechazadas'] ?></span>
                    </div>
                    <?php if ((float)$chartRendiciones['declarado'] > 0): ?>
                    <div class="pt-3 border-t border-slate-100 text-xs text-slate-400 font-medium">
                        Declarado: <strong class="text-slate-700"><?= MoneyHelper::formatShort((float)$chartRendiciones['declarado']) ?></strong>
                        · Recibido: <strong class="text-slate-700"><?= MoneyHelper::formatShort((float)$chartRendiciones['recibido']) ?></strong>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="h-32 flex flex-col items-center justify-center text-slate-400">
                <i class="isax isax-wallet-money text-3xl mb-2 opacity-30"></i>
                <p class="text-sm font-medium">Sin rendiciones en los últimos 30 días.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- TOP 10 DEUDORES -->
    <?php if (!empty($top10Deudores)): ?>
    <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i class="isax isax-people text-rose-500"></i> Top 10 deudores
            </h2>
            <a href="<?= url('admin/reportes/mora') ?>" class="text-xs font-bold text-brand-600 hover:text-brand-800 transition-colors">
                Ver reporte completo →
            </a>
        </div>
        <div class="divide-y divide-slate-50">
            <?php foreach ($top10Deudores as $i => $d): ?>
            <div class="flex items-center gap-4 px-6 py-3 hover:bg-slate-50/60 transition-colors">
                <span class="text-sm font-bold text-slate-300 w-5 shrink-0 text-center"><?= $i + 1 ?></span>
                <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-sm shrink-0">
                    <?= mb_strtoupper(mb_substr($d['cliente'], 0, 1)) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <a href="<?= url('creditos/' . $d['credito_id']) ?>"
                       class="font-bold text-slate-800 text-sm hover:text-brand-600 transition-colors truncate block">
                        <?= e($d['cliente']) ?>
                    </a>
                </div>
                <div class="text-right shrink-0">
                    <p class="font-bold text-slate-900 text-sm" style="font-family:'Outfit',sans-serif;">
                        <?= MoneyHelper::formatShort((float)$d['saldo_capital'] + (float)$d['mora_pendiente']) ?>
                    </p>
                    <?php if ((float)$d['mora_pendiente'] > 0): ?>
                    <p class="text-[10px] font-bold text-red-500">
                        mora: <?= MoneyHelper::formatShort((float)$d['mora_pendiente']) ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ACCIONES RÁPIDAS -->
    <div>
        <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-3 px-1">Acciones rápidas</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">

            <a href="<?= url('admin/rendiciones?estado=pendiente') ?>"
               class="group flex items-center gap-3 p-4 rounded-2xl bg-white border border-slate-100 hover:border-emerald-200 shadow-sm hover:shadow-md transition-all">
                <div class="w-9 h-9 shrink-0 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all">
                    <i class="isax isax-card-tick text-lg"></i>
                </div>
                <span class="text-sm font-bold text-slate-700">Rendiciones</span>
            </a>

            <a href="<?= url('admin/pagos') ?>"
               class="group flex items-center gap-3 p-4 rounded-2xl bg-white border border-slate-100 hover:border-brand-200 shadow-sm hover:shadow-md transition-all">
                <div class="w-9 h-9 shrink-0 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center group-hover:bg-brand-600 group-hover:text-white transition-all">
                    <i class="isax isax-receipt-item text-lg"></i>
                </div>
                <span class="text-sm font-bold text-slate-700">Pagos</span>
            </a>

            <a href="<?= url('admin/reportes/cartera') ?>"
               class="group flex items-center gap-3 p-4 rounded-2xl bg-white border border-slate-100 hover:border-blue-200 shadow-sm hover:shadow-md transition-all">
                <div class="w-9 h-9 shrink-0 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all">
                    <i class="isax isax-chart text-lg"></i>
                </div>
                <span class="text-sm font-bold text-slate-700">Cartera</span>
            </a>

            <a href="<?= url('admin/reportes/cobradores') ?>"
               class="group flex items-center gap-3 p-4 rounded-2xl bg-white border border-slate-100 hover:border-violet-200 shadow-sm hover:shadow-md transition-all">
                <div class="w-9 h-9 shrink-0 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center group-hover:bg-violet-600 group-hover:text-white transition-all">
                    <i class="isax isax-profile-2user text-lg"></i>
                </div>
                <span class="text-sm font-bold text-slate-700">Productividad</span>
            </a>
        </div>
    </div>

</div>

<?php
// CSS vars for Chart.js from brand palette
$brandColor = '#6C5DD3';
?>
<script>
(function () {
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const animDuration = prefersReduced ? 0 : 500;

    const cobranzaData = <?= $cobranzaJson ?>;
    const moraData     = <?= $moraJson ?>;
    const rendData     = <?= $rendicionesJson ?>;

    // ── Cobranza line chart ──────────────────────────────────
    const canvasC = document.getElementById('chartCobranza');
    if (canvasC && cobranzaData.length) {
        const ctx = canvasC.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, 'rgba(108,93,211,0.18)');
        gradient.addColorStop(1, 'rgba(108,93,211,0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: cobranzaData.map(d => d.fecha),
                datasets: [{
                    data: cobranzaData.map(d => d.total),
                    borderColor: '<?= $brandColor ?>',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    tension: 0.35,
                    fill: true,
                }],
            },
            options: {
                animation: { duration: animDuration },
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: {
                    callbacks: {
                        label: ctx => ' $' + ctx.parsed.y.toLocaleString('es-AR', { minimumFractionDigits: 2 }),
                    }
                }},
                scales: {
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 10, font: { size: 11 } } },
                    y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: {
                        font: { size: 11 },
                        callback: v => '$' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v),
                    }},
                },
            },
        });
    }

    // ── Mora horizontal bar ──────────────────────────────────
    const canvasM = document.getElementById('chartMora');
    if (canvasM && moraData.length) {
        new Chart(canvasM.getContext('2d'), {
            type: 'bar',
            data: {
                labels: moraData.map(d => d.sucursal),
                datasets: [{
                    data: moraData.map(d => parseFloat(d.mora)),
                    backgroundColor: 'rgba(239,68,68,0.75)',
                    borderRadius: 6,
                    borderSkipped: false,
                }],
            },
            options: {
                animation: { duration: animDuration },
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: {
                    callbacks: {
                        label: ctx => ' $' + ctx.parsed.x.toLocaleString('es-AR', { minimumFractionDigits: 2 }),
                    }
                }},
                scales: {
                    x: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { size: 11 },
                        callback: v => '$' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v) } },
                    y: { grid: { display: false }, ticks: { font: { size: 11 } } },
                },
            },
        });
    }

    // ── Rendiciones donut ────────────────────────────────────
    const canvasR = document.getElementById('chartRendiciones');
    if (canvasR && (rendData.confirmadas || rendData.pendientes || rendData.rechazadas)) {
        new Chart(canvasR.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Confirmadas', 'Pendientes', 'Rechazadas'],
                datasets: [{
                    data: [rendData.confirmadas, rendData.pendientes, rendData.rechazadas],
                    backgroundColor: ['#10b981', '#f59e0b', '#f43f5e'],
                    borderWidth: 0,
                    hoverOffset: 4,
                }],
            },
            options: {
                animation: { duration: animDuration },
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                cutout: '70%',
            },
        });
    }
})();
</script>
