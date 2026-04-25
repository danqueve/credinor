<?php // views/auth/login.php ?>
<div class="w-full max-w-sm">
    <!-- Logo / título -->
    <div class="text-center mb-8">
        <div class="text-5xl mb-3">💰</div>
        <h1 class="text-2xl font-bold text-white">Crédinor</h1>
        <p class="text-brand-200 text-sm mt-1">Sistema de Préstamos</p>
    </div>

    <!-- Card de login -->
    <div class="bg-white rounded-2xl shadow-xl p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-5 text-center">Iniciar sesión</h2>

        <?php if (\App\Core\Session::hasFlash('error')): ?>
            <div class="alert-error mb-4">
                <span>❌</span>
                <?= e(\App\Core\Session::getFlash('error')) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('login') ?>" novalidate>
            <?= csrf_field() ?>

            <div class="mb-4">
                <label for="username" class="form-label">Usuario</label>
                <input id="username"
                       type="text"
                       name="username"
                       class="form-input"
                       placeholder="nombre_usuario"
                       autocomplete="username"
                       required
                       value="<?= e($_POST['username'] ?? '') ?>">
            </div>

            <div class="mb-6">
                <label for="password" class="form-label">Contraseña</label>
                <input id="password"
                       type="password"
                       name="password"
                       class="form-input"
                       placeholder="••••••••"
                       autocomplete="current-password"
                       required>
            </div>

            <button type="submit" class="btn-primary w-full justify-center py-2.5">
                Ingresar
            </button>
        </form>
    </div>

    <p class="text-center text-brand-300 text-xs mt-6">
        Crédinor &copy; <?= date('Y') ?>
    </p>
</div>
