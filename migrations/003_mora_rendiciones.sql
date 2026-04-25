-- migrations/003_mora_devengada.sql
-- Tabla para registrar el devengamiento diario de mora (idempotente)

CREATE TABLE IF NOT EXISTS mora_devengada (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cuota_id    INT UNSIGNED NOT NULL,
    fecha       DATE         NOT NULL,
    saldo_base  DECIMAL(12,2) NOT NULL,
    porcentaje  DECIMAL(6,4) NOT NULL,
    monto_mora  DECIMAL(12,2) NOT NULL,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_mora_cuota_fecha (cuota_id, fecha),
    KEY idx_mora_fecha (fecha),
    CONSTRAINT fk_mora_cuota FOREIGN KEY (cuota_id) REFERENCES cuotas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Columna rendicion_id en pagos (si no existe)
ALTER TABLE pagos
    ADD COLUMN IF NOT EXISTS rendicion_id INT UNSIGNED NULL AFTER estado,
    ADD KEY IF NOT EXISTS idx_pago_rendicion (rendicion_id);

-- Tabla rendiciones (si no existe)
CREATE TABLE IF NOT EXISTS rendiciones (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cobrador_id     INT UNSIGNED NOT NULL,
    sucursal_id     INT UNSIGNED NOT NULL,
    fecha           DATE         NOT NULL,
    monto_declarado DECIMAL(12,2) NOT NULL,
    monto_recibido  DECIMAL(12,2) NULL,
    estado          ENUM('pendiente','confirmada','rechazada') NOT NULL DEFAULT 'pendiente',
    admin_id        INT UNSIGNED NULL,
    observaciones   TEXT NULL,
    confirmado_at   TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    KEY idx_rendicion_cobrador (cobrador_id),
    KEY idx_rendicion_estado (estado),
    CONSTRAINT fk_rendicion_cobrador FOREIGN KEY (cobrador_id) REFERENCES usuarios(id),
    CONSTRAINT fk_rendicion_sucursal FOREIGN KEY (sucursal_id) REFERENCES sucursales(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Config: porcentaje de mora diaria global
INSERT IGNORE INTO config (clave, valor, descripcion)
VALUES ('porcentaje_mora_diaria_default', '0.1', 'Porcentaje diario de mora (0.1 = 0.1% diario)');
