<?php // views/cobrador/rendicion_detalle.php
use App\Helpers\MoneyHelper;
use App\Helpers\DateHelper;

$badges = [
    'pendiente' => 'badge-pendiente',
    'confirmada' => 'badge-activo',
    'rechazada' => 'badge-rechazado',
];
?>
<div class="max-w-lg mx-auto space-y-6 pb-24">

    <!-- Header Mobile -->
    <div class="flex items-center gap-4 mb-2">
        <a href="<?= url('cobrador/rendiciones') ?>" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:bg-brand-50 transition-all active:scale-95 shadow-sm">
            <i class="isax isax-arrow-left-2 text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900" style="font-family: 'Outfit', sans-serif;">Detalle de Cierre</h1>
            <p class="text-slate-500 font-medium text-xs">Rendición #<?= $rendicion['id'] ?></p>
        </div>
    </div>

    <!-- Info principal -->
    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 border border-slate-700 shadow-xl relative overflow-hidden text-white">
        <div class="absolute top-0 right-0 p-6 opacity-10 pointer-events-none">
            <i class="isax isax-wallet-money text-8xl text-white"></i>
        </div>
        
        <div class="flex justify-between items-start mb-6 relative z-10">
            <div>
                <span class="block text-xs font-medium text-slate-400 mb-0.5">Fecha del cierre</span>
                <span class="font-bold text-lg"><?= DateHelper::formatoArg($rendicion['fecha']) ?></span>
            </div>
            <span class="<?= $badges[$rendicion['estado']] ?? 'badge' ?> border-0">
                <?= ucfirst($rendicion['estado']) ?>
            </span>
        </div>
        
        <div class="grid grid-cols-2 gap-4 relative z-10">
            <div class="bg-white/5 rounded-2xl p-4 border border-white/10 backdrop-blur-sm">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Monto Declarado</span>
                <span class="block text-2xl font-extrabold text-white tracking-tight" style="font-family: 'Outfit', sans-serif;">
                    <?= MoneyHelper::format((float)$rendicion['monto_declarado']) ?>
                </span>
            </div>
            <div class="bg-white/5 rounded-2xl p-4 border border-white/10 backdrop-blur-sm">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Pagos incluídos</span>
                <span class="block text-2xl font-extrabold text-emerald-400 tracking-tight" style="font-family: 'Outfit', sans-serif;">
                    <?= count($rendicion['pagos'] ?? []) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Observaciones -->
    <?php if ($rendicion['observaciones']): ?>
    <div class="bg-amber-50 rounded-2xl p-4 border border-amber-100 flex gap-3 items-start">
        <i class="isax isax-message-text-1 text-amber-500 mt-0.5"></i>
        <div>
            <span class="block text-xs font-bold uppercase tracking-wider text-amber-700 mb-1">Nota adjunta</span>
            <p class="text-sm text-amber-900 font-medium"><?= e($rendicion['observaciones']) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Lista de Pagos -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i class="isax isax-receipt-item text-brand-500"></i> Desglose de Pagos
            </h2>
        </div>
        
        <?php if (empty($rendicion['pagos'])): ?>
            <div class="p-8 text-center text-slate-500 font-medium text-sm">
                No hay pagos en esta rendición.
            </div>
        <?php else: ?>
            <div class="divide-y divide-slate-50">
                <?php foreach ($rendicion['pagos'] as $pago): ?>
                <div class="p-4 hover:bg-slate-50 transition-colors">
                    <div class="flex justify-between items-start mb-1">
                        <div>
                            <span class="block font-bold text-slate-800 text-sm"><?= e($pago['cliente_nombre']) ?></span>
                            <span class="text-xs text-slate-500 font-medium flex items-center gap-1 mt-0.5">
                                <i class="isax isax-document-text text-slate-400"></i> Crédito #<?= $pago['credito_id'] ?> - Cuota #<?= $pago['numero_cuota'] ?>
                            </span>
                        </div>
                        <span class="font-extrabold text-brand-600 tracking-tight" style="font-family: 'Outfit', sans-serif;">
                            <?= MoneyHelper::formatShort((float)$pago['monto']) ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <?= date('d/m/Y H:i', strtotime($pago['created_at'])) ?>
                        </span>
                        <?php if ($pago['metodo_pago'] === 'transferencia'): ?>
                            <span class="text-[10px] font-bold text-purple-600 bg-purple-50 border border-purple-100 px-2 py-0.5 rounded flex items-center gap-1 uppercase tracking-wider">
                                <i class="isax isax-card-tick"></i> Transf.
                            </span>
                        <?php else: ?>
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded flex items-center gap-1 uppercase tracking-wider">
                                <i class="isax isax-money-tick"></i> Efvo.
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
