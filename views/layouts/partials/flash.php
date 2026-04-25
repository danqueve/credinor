<?php
// views/layouts/partials/flash.php
use App\Core\Session;
?>
<?php if (Session::hasFlash('success')): ?>
    <div class="alert-success mb-4" role="alert">
        <span>✅</span>
        <?= e(Session::getFlash('success')) ?>
    </div>
<?php endif; ?>

<?php if (Session::hasFlash('error')): ?>
    <div class="alert-error mb-4" role="alert">
        <span>❌</span>
        <?= e(Session::getFlash('error')) ?>
    </div>
<?php endif; ?>

<?php if (Session::hasFlash('warning')): ?>
    <div class="alert-warning mb-4" role="alert">
        <span>⚠️</span>
        <?= e(Session::getFlash('warning')) ?>
    </div>
<?php endif; ?>

<?php if (Session::hasFlash('info')): ?>
    <div class="alert-info mb-4" role="alert">
        <span>ℹ️</span>
        <?= e(Session::getFlash('info')) ?>
    </div>
<?php endif; ?>
