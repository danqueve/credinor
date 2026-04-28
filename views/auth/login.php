<?php // views/auth/login.php ?>
<div class="w-full max-w-[400px] relative z-10">
    <!-- Logo / título -->
    <div class="text-center mb-8">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-brand-600 text-white flex items-center justify-center shadow-lg shadow-brand-500/30 mb-4">
            <i class="isax isax-wallet-money text-3xl"></i>
        </div>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">Crédinor</h1>
        <p class="text-slate-500 text-sm font-medium mt-1">Plataforma de Gestión</p>
    </div>

    <!-- Card de login -->
    <div class="bg-white/80 backdrop-blur-xl rounded-3xl border border-white shadow-[0_8px_30px_rgb(0,0,0,0.08)] p-8">
        <h2 class="text-xl font-bold text-slate-800 mb-6 text-center" style="font-family: 'Outfit', sans-serif;">Iniciar Sesión</h2>

        <?php if (\App\Core\Session::hasFlash('error')): ?>
            <div class="bg-red-50 text-red-700 p-3 rounded-xl text-sm font-medium mb-6 flex items-start gap-2 border border-red-100">
                <i class="isax isax-warning-2 mt-0.5 shrink-0"></i>
                <?= e(\App\Core\Session::getFlash('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($blockedUntil)): ?>
            <div class="bg-amber-50 text-amber-800 p-3 rounded-xl text-sm font-medium mb-6 flex items-start gap-2 border border-amber-200">
                <i class="isax isax-clock mt-0.5 shrink-0"></i>
                Acceso bloqueado temporalmente por múltiples intentos fallidos.
                Reintentá a las <?= date('H:i', $blockedUntil) ?>.
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('login') ?>" novalidate
              <?= !empty($blockedUntil) ? 'onsubmit="return false"' : '' ?>>
            <?= csrf_field() ?>

            <div class="mb-5">
                <label for="username" class="form-label">Usuario</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="isax isax-user text-slate-400"></i>
                    </div>
                    <input id="username"
                           type="text"
                           name="username"
                           class="form-input pl-10"
                           placeholder="nombre_usuario"
                           autocomplete="username"
                           required
                           value="<?= e($_POST['username'] ?? '') ?>">
                </div>
            </div>

            <div class="mb-8">
                <label for="password" class="form-label">Contraseña</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="isax isax-lock-1 text-slate-400"></i>
                    </div>
                    <input id="password"
                           type="password"
                           name="password"
                           class="form-input pl-10"
                           placeholder="••••••••"
                           autocomplete="current-password"
                           required>
                </div>
            </div>

            <button type="submit" class="btn-primary w-full justify-center py-3 text-base">
                Ingresar a la plataforma
                <i class="isax isax-arrow-right-1"></i>
            </button>
        </form>
    </div>

    <p class="text-center text-slate-400 text-xs mt-8 font-medium">
        Crédinor &copy; <?= date('Y') ?>
    </p>
</div>
