-- 008: tabla de log de autenticación
CREATE TABLE IF NOT EXISTS auth_log (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip         VARCHAR(45)  NOT NULL,
    username   VARCHAR(100) NOT NULL DEFAULT '',
    evento     ENUM('login_ok','login_fail','logout','lockout') NOT NULL,
    user_agent VARCHAR(255) NOT NULL DEFAULT '',
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip_ts       (ip, created_at),
    INDEX idx_username_ts (username, created_at),
    INDEX idx_evento_ts   (evento, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
