<?php
// views/layouts/partials/flash.php — Alpine auto-dismiss toasts
use App\Core\Session;

$flashes = [];
foreach (['success', 'error', 'warning', 'info'] as $type) {
    if (Session::hasFlash($type)) {
        $flashes[] = ['type' => $type, 'msg' => Session::getFlash($type)];
    }
}
if (empty($flashes)) return;

$iconMap  = ['success' => 'isax-tick-circle', 'error' => 'isax-warning-2', 'warning' => 'isax-warning', 'info' => 'isax-info-circle'];
$colorMap = [
    'success' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
    'error'   => 'bg-red-50 border-red-200 text-red-800',
    'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
    'info'    => 'bg-blue-50 border-blue-200 text-blue-800',
];
$iconColorMap = ['success' => 'text-emerald-500', 'error' => 'text-red-500', 'warning' => 'text-amber-500', 'info' => 'text-blue-500'];
?>
<div class="fixed top-4 right-4 z-[100] flex flex-col gap-2 w-full max-w-sm pointer-events-none" aria-live="polite">
    <?php foreach ($flashes as $flash): ?>
    <div x-data="{ show: true }"
         x-init="setTimeout(() => show = false, 4500)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-x-4 scale-95"
         x-transition:enter-end="opacity-100 translate-x-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="pointer-events-auto flex items-start gap-3 p-4 rounded-2xl border shadow-xl text-sm font-medium <?= $colorMap[$flash['type']] ?>"
         role="alert"
         x-cloak>
        <i class="isax <?= $iconMap[$flash['type']] ?> <?= $iconColorMap[$flash['type']] ?> text-xl shrink-0 mt-0.5"></i>
        <p class="flex-1 leading-snug"><?= e($flash['msg']) ?></p>
        <button @click="show = false"
                class="shrink-0 ml-1 opacity-40 hover:opacity-70 transition-opacity"
                aria-label="Cerrar">
            <i class="isax isax-close-circle text-lg"></i>
        </button>
    </div>
    <?php endforeach; ?>
</div>
