<?php
// src/Controllers/AuthController.php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Helpers\Logger;
use App\Models\Usuario;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS  = 5;
    private const LOCKOUT_MINS  = 15;

    public function loginForm(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        $blockedUntil = $this->lockedUntil($this->clientIp());
        $this->view('auth/login', ['blockedUntil' => $blockedUntil], 'auth');
    }

    public function login(): void
    {
        $this->validateCsrf();

        $ip       = $this->clientIp();
        $username = trim(Request::post('username', ''));
        $password = Request::post('password', '');

        // Verificar bloqueo
        $blockedUntil = $this->lockedUntil($ip);
        if ($blockedUntil !== null) {
            $mins = (int) ceil(($blockedUntil - time()) / 60);
            Logger::auth('lockout', $ip, $username);
            Session::flash('error', "Demasiados intentos fallidos. Intentá de nuevo en {$mins} minuto(s).");
            Response::redirect('/login');
        }

        if (!$username || !$password) {
            Session::flash('error', 'Ingresá usuario y contraseña.');
            Response::redirect('/login');
        }

        $usuario = new Usuario();
        $user    = $usuario->findByUsername($username);

        if (!$user || !Auth::verifyPassword($password, $user['password'])) {
            $this->recordAttempt($ip, $username);
            Logger::auth('login_fail', $ip, $username);
            Session::flash('error', 'Credenciales incorrectas.');
            Response::redirect('/login');
        }

        if ((int)$user['activo'] !== 1) {
            Session::flash('error', 'Tu cuenta está deshabilitada. Contactá al administrador.');
            Response::redirect('/login');
        }

        // Login exitoso: limpiar intentos y registrar
        $this->clearAttempts($ip);
        Logger::auth('login_ok', $ip, $username, (int) $user['id']);
        Auth::login($user);
        Response::redirect('/dashboard');
    }

    public function logout(): void
    {
        Logger::auth('logout', $this->clientIp(), \App\Core\Auth::user()['username'] ?? '');
        Auth::logout();
        Response::redirect('/login');
    }

    // ──────────────────────────────────────────────────────────────
    // Rate limiting helpers
    // ──────────────────────────────────────────────────────────────

    private function lockedUntil(string $ip): ?int
    {
        $this->ensureAttemptsTable();
        $pdo    = Database::getInstance();
        $cutoff = date('Y-m-d H:i:s', time() - self::LOCKOUT_MINS * 60);

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND created_at >= ?");
        $stmt->execute([$ip, $cutoff]);
        $count = (int) $stmt->fetchColumn();

        if ($count >= self::MAX_ATTEMPTS) {
            $stmt2 = $pdo->prepare(
                "SELECT created_at FROM login_attempts
                 WHERE ip = ? AND created_at >= ?
                 ORDER BY created_at ASC LIMIT 1"
            );
            $stmt2->execute([$ip, $cutoff]);
            $oldest = $stmt2->fetchColumn();

            return strtotime($oldest) + self::LOCKOUT_MINS * 60;
        }

        return null;
    }

    private function recordAttempt(string $ip, string $username): void
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare("INSERT INTO login_attempts (ip, username) VALUES (?, ?)");
        $stmt->execute([$ip, $username]);
    }

    private function clearAttempts(string $ip): void
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE ip = ?");
        $stmt->execute([$ip]);
    }

    private function ensureAttemptsTable(): void
    {
        Database::getInstance()->exec(
            "CREATE TABLE IF NOT EXISTS login_attempts (
                id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                ip         VARCHAR(45) NOT NULL,
                username   VARCHAR(100) NOT NULL DEFAULT '',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_ip_ts (ip, created_at),
                INDEX idx_username_ts (username, created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
    }

    private function clientIp(): string
    {
        // Confiar en X-Forwarded-For solo si hay un proxy de confianza configurado
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
