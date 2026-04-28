<?php
// src/Helpers/Logger.php
namespace App\Helpers;

use App\Core\Database;

class Logger
{
    /**
     * Registra un evento en creditos_log.
     * @param string $entidad  'credito', 'pago', 'rendicion', etc.
     * @param int    $entidadId
     * @param string $accion   'crear', 'autorizar', 'cancelar', 'pago_registrar', 'pago_anular', etc.
     * @param array  $payload  Datos adicionales serializados como JSON.
     * @param int|null $usuarioId  Null → intenta leer de la sesión activa.
     */
    public static function write(
        string $entidad,
        int    $entidadId,
        string $accion,
        array  $payload = [],
        ?int   $usuarioId = null
    ): void {
        try {
            $pdo  = Database::getInstance();
            $uid  = $usuarioId ?? (\App\Core\Auth::id() ?: 0);

            // Verificar que la tabla existe (auto-bootstrap)
            $pdo->exec(
                "CREATE TABLE IF NOT EXISTS creditos_log (
                    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    credito_id  INT UNSIGNED NULL,
                    usuario_id  INT UNSIGNED NULL,
                    accion      VARCHAR(60) NOT NULL,
                    entidad     VARCHAR(40) NOT NULL DEFAULT 'credito',
                    entidad_id  INT UNSIGNED NULL,
                    payload     JSON NULL,
                    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_credito (credito_id),
                    INDEX idx_accion  (accion),
                    INDEX idx_ts      (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );

            $stmt = $pdo->prepare(
                "INSERT INTO creditos_log (credito_id, usuario_id, accion, entidad, entidad_id, payload)
                 VALUES (:credito_id, :usuario_id, :accion, :entidad, :entidad_id, :payload)"
            );
            $stmt->execute([
                ':credito_id' => $entidad === 'credito' ? $entidadId : null,
                ':usuario_id' => $uid ?: null,
                ':accion'     => $accion,
                ':entidad'    => $entidad,
                ':entidad_id' => $entidadId,
                ':payload'    => $payload ? json_encode($payload, JSON_UNESCAPED_UNICODE) : null,
            ]);
        } catch (\Throwable $e) {
            // El logging nunca debe romper el flujo principal
            error_log('[Logger] ' . $e->getMessage());
        }
    }

    /**
     * Registra un evento de autenticación en auth_log.
     */
    public static function auth(
        string $evento,
        string $ip,
        string $username = '',
        ?int   $userId   = null
    ): void {
        try {
            $pdo = Database::getInstance();

            $pdo->exec(
                "CREATE TABLE IF NOT EXISTS auth_log (
                    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    ip         VARCHAR(45)  NOT NULL,
                    username   VARCHAR(100) NOT NULL DEFAULT '',
                    evento     VARCHAR(30)  NOT NULL,
                    user_agent VARCHAR(255) NOT NULL DEFAULT '',
                    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_ip_ts       (ip, created_at),
                    INDEX idx_username_ts (username, created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );

            $stmt = $pdo->prepare(
                "INSERT INTO auth_log (ip, username, evento, user_agent) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([
                $ip,
                $username,
                $evento,
                substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            ]);
        } catch (\Throwable $e) {
            error_log('[Logger::auth] ' . $e->getMessage());
        }
    }
}
