<?php
/**
 * views/partials/empty_state.php
 *
 * Componente reutilizable para listas vacías.
 *
 * Variables esperadas (pasar via compact o extract antes de include):
 *   string $icon        — Clase de Iconsax, ej. 'isax-profile-2user'
 *   string $title       — Título del estado vacío
 *   string $description — Descripción
 *   string $actionLabel — (opcional) Texto del botón de acción
 *   string $actionUrl   — (opcional) URL del botón de acción
 *
 * Uso:
 *   <?php include ROOT_PATH . '/views/partials/empty_state.php'; ?>
 *   con variables $icon, $title, $description ya definidas en scope.
 */
$icon        ??= 'isax-document';
$title       ??= 'Sin resultados';
$description ??= 'No se encontraron registros.';
$actionLabel ??= '';
$actionUrl   ??= '';
?>
<div class="bg-white rounded-3xl p-10 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center">
    <div class="w-20 h-20 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-5">
        <i class="isax <?= e($icon) ?> text-4xl"></i>
    </div>
    <h3 class="text-lg font-bold text-slate-800 mb-2"><?= e($title) ?></h3>
    <p class="text-slate-500 font-medium max-w-xs mx-auto"><?= e($description) ?></p>
    <?php if ($actionLabel && $actionUrl): ?>
        <a href="<?= e($actionUrl) ?>" class="btn-secondary mt-6 inline-flex"><?= e($actionLabel) ?></a>
    <?php endif; ?>
</div>
