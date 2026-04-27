-- =============================================================
-- 000_full_install.sql — Instalación completa de Crédinor
-- Equivale a ejecutar migrations 001 al 006 + seed en orden.
-- Uso: mysql -u root -p credinor < migrations/000_full_install.sql
-- =============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

-- ============================================================
-- SUCURSALES
-- ============================================================
CREATE TABLE IF NOT EXISTS sucursales (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    direccion   VARCHAR(200),
    telefono    VARCHAR(20),
    activa      TINYINT(1) NOT NULL DEFAULT 1,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- USUARIOS  (columna username, sin email — migración 004)
-- ============================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sucursal_id   INT UNSIGNED NOT NULL,
    username      VARCHAR(50)  NOT NULL,
    nombre        VARCHAR(100) NOT NULL,
    password      VARCHAR(255) NOT NULL,
    rol           ENUM('admin','vendedor','cobrador') NOT NULL,
    activo        TINYINT(1)   NOT NULL DEFAULT 1,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_sucursal FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
    UNIQUE KEY uq_usuarios_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_usuarios_rol      ON usuarios(rol);
CREATE INDEX idx_usuarios_sucursal ON usuarios(sucursal_id);

-- ============================================================
-- CLIENTES
-- ============================================================
CREATE TABLE IF NOT EXISTS clientes (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sucursal_id   INT UNSIGNED NOT NULL,
    vendedor_id   INT UNSIGNED NOT NULL,
    dni           VARCHAR(20)  NOT NULL,
    nombre        VARCHAR(150) NOT NULL,
    telefono      VARCHAR(30),
    email         VARCHAR(150),
    domicilio     VARCHAR(250),
    localidad     VARCHAR(100),
    lat           DECIMAL(10,7),
    lng           DECIMAL(10,7),
    observaciones TEXT,
    activo        TINYINT(1)   NOT NULL DEFAULT 1,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_clientes_sucursal FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
    CONSTRAINT fk_clientes_vendedor FOREIGN KEY (vendedor_id) REFERENCES usuarios(id),
    UNIQUE KEY uq_clientes_dni (dni)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_clientes_sucursal ON clientes(sucursal_id);

-- ============================================================
-- GARANTES
-- ============================================================
CREATE TABLE IF NOT EXISTS garantes (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id    INT UNSIGNED,
    dni           VARCHAR(20)  NOT NULL,
    nombre        VARCHAR(150) NOT NULL,
    telefono      VARCHAR(30),
    domicilio     VARCHAR(250),
    observaciones TEXT,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_garantes_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- CRÉDITOS  (aplica_mora DEFAULT 0 — migración 005)
-- ============================================================
CREATE TABLE IF NOT EXISTS creditos (
    id                     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sucursal_id            INT UNSIGNED NOT NULL,
    cliente_id             INT UNSIGNED NOT NULL,
    vendedor_id            INT UNSIGNED NOT NULL,
    cobrador_id            INT UNSIGNED,
    garante_id             INT UNSIGNED,
    monto_prestado         DECIMAL(12,2) NOT NULL,
    monto_a_devolver       DECIMAL(12,2) NOT NULL,
    cantidad_cuotas        SMALLINT UNSIGNED NOT NULL,
    frecuencia             ENUM('diaria','semanal','quincenal','mensual') NOT NULL,
    fecha_inicio           DATE NOT NULL,
    fecha_primera_cuota    DATE NOT NULL,
    aplica_mora            TINYINT(1)    NOT NULL DEFAULT 0,
    porcentaje_mora_diaria DECIMAL(5,4),
    mora_acumulada         DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    mora_pagada            DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    estado                 ENUM('pendiente_autorizacion','activo','finalizado','rechazado','cancelado') NOT NULL DEFAULT 'pendiente_autorizacion',
    motivo_rechazo         TEXT,
    observaciones          TEXT,
    created_at             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_creditos_sucursal FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
    CONSTRAINT fk_creditos_cliente  FOREIGN KEY (cliente_id)  REFERENCES clientes(id),
    CONSTRAINT fk_creditos_vendedor FOREIGN KEY (vendedor_id) REFERENCES usuarios(id),
    CONSTRAINT fk_creditos_cobrador FOREIGN KEY (cobrador_id) REFERENCES usuarios(id),
    CONSTRAINT fk_creditos_garante  FOREIGN KEY (garante_id)  REFERENCES garantes(id) ON DELETE SET NULL,
    CONSTRAINT chk_montos  CHECK (monto_a_devolver >= monto_prestado),
    CONSTRAINT chk_cuotas  CHECK (cantidad_cuotas > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_creditos_estado   ON creditos(estado);
CREATE INDEX idx_creditos_cobrador ON creditos(cobrador_id);
CREATE INDEX idx_creditos_cliente  ON creditos(cliente_id);
CREATE INDEX idx_creditos_sucursal ON creditos(sucursal_id);

-- ============================================================
-- CUOTAS  (estado incluye 'cancelada' — migración 006)
-- ============================================================
CREATE TABLE IF NOT EXISTS cuotas (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    credito_id       INT UNSIGNED NOT NULL,
    numero_cuota     SMALLINT UNSIGNED NOT NULL,
    monto            DECIMAL(12,2) NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    estado           ENUM('pendiente','parcial','pagada','vencida','cancelada') NOT NULL DEFAULT 'pendiente',
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_cuotas_credito FOREIGN KEY (credito_id) REFERENCES creditos(id) ON DELETE CASCADE,
    UNIQUE KEY uq_cuota_numero (credito_id, numero_cuota)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_cuotas_fecha  ON cuotas(fecha_vencimiento);
CREATE INDEX idx_cuotas_estado ON cuotas(estado);

-- ============================================================
-- RENDICIONES  (debe crearse antes que pagos por la FK)
-- ============================================================
CREATE TABLE IF NOT EXISTS rendiciones (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cobrador_id     INT UNSIGNED NOT NULL,
    sucursal_id     INT UNSIGNED NOT NULL,
    fecha           DATE         NOT NULL,
    monto_declarado DECIMAL(12,2) NOT NULL,
    monto_recibido  DECIMAL(12,2) NULL,
    diferencia      DECIMAL(12,2) GENERATED ALWAYS AS (monto_recibido - monto_declarado) STORED,
    estado          ENUM('pendiente','confirmada','rechazada') NOT NULL DEFAULT 'pendiente',
    observaciones   TEXT NULL,
    admin_id        INT UNSIGNED NULL,
    confirmado_at   DATETIME     NULL,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_rendiciones_cobrador FOREIGN KEY (cobrador_id) REFERENCES usuarios(id),
    CONSTRAINT fk_rendiciones_sucursal FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
    CONSTRAINT fk_rendiciones_admin    FOREIGN KEY (admin_id)    REFERENCES usuarios(id),
    UNIQUE KEY uq_rendicion_dia (cobrador_id, fecha),
    KEY idx_rendicion_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PAGOS  (incluye rendicion_id, metodo_pago, campos anulación)
-- ============================================================
CREATE TABLE IF NOT EXISTS pagos (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cuota_id         INT UNSIGNED NOT NULL,
    cobrador_id      INT UNSIGNED NOT NULL,
    rendicion_id     INT UNSIGNED NULL,
    monto            DECIMAL(12,2) NOT NULL,
    monto_a_capital  DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    monto_a_mora     DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    metodo_pago      ENUM('efectivo','transferencia') NOT NULL DEFAULT 'efectivo',
    estado           ENUM('pendiente_rendir','rendido','confirmado','anulado') NOT NULL DEFAULT 'pendiente_rendir',
    anulado_at       DATETIME     NULL,
    anulado_por      INT UNSIGNED NULL,
    motivo_anulacion VARCHAR(255) NULL,
    observaciones    TEXT,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pagos_cuota     FOREIGN KEY (cuota_id)     REFERENCES cuotas(id),
    CONSTRAINT fk_pagos_cobrador  FOREIGN KEY (cobrador_id)  REFERENCES usuarios(id),
    CONSTRAINT fk_pagos_rendicion FOREIGN KEY (rendicion_id) REFERENCES rendiciones(id) ON DELETE SET NULL,
    CONSTRAINT chk_monto_positivo CHECK (monto > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_pagos_cuota     ON pagos(cuota_id);
CREATE INDEX idx_pagos_cobrador  ON pagos(cobrador_id);
CREATE INDEX idx_pagos_rendicion ON pagos(rendicion_id);
CREATE INDEX idx_pagos_estado    ON pagos(estado);

-- ============================================================
-- MORA DEVENGADA
-- ============================================================
CREATE TABLE IF NOT EXISTS mora_devengada (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cuota_id   INT UNSIGNED NOT NULL,
    fecha      DATE         NOT NULL,
    saldo_base DECIMAL(12,2) NOT NULL,
    porcentaje DECIMAL(6,4)  NOT NULL,
    monto_mora DECIMAL(12,2) NOT NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mora_cuota FOREIGN KEY (cuota_id) REFERENCES cuotas(id) ON DELETE CASCADE,
    UNIQUE KEY uq_mora_cuota_fecha (cuota_id, fecha),
    KEY idx_mora_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- LOG DE CRÉDITOS
-- ============================================================
CREATE TABLE IF NOT EXISTS creditos_log (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    credito_id   INT UNSIGNED NOT NULL,
    usuario_id   INT UNSIGNED NOT NULL,
    estado_desde VARCHAR(50),
    estado_hasta VARCHAR(50),
    nota         TEXT,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_log_credito FOREIGN KEY (credito_id) REFERENCES creditos(id),
    CONSTRAINT fk_log_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_log_credito ON creditos_log(credito_id);

-- ============================================================
-- CONFIG GLOBAL
-- ============================================================
CREATE TABLE IF NOT EXISTS config (
    clave       VARCHAR(100) PRIMARY KEY,
    valor       TEXT         NOT NULL,
    descripcion VARCHAR(255),
    updated_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- SEED — Datos iniciales
-- ============================================================

INSERT INTO sucursales (nombre, direccion, telefono) VALUES
('Casa Central', 'Av. Principal 123', '299-4000000');

-- usuario: admin / contraseña: Admin1234!  ← cambiar al primer ingreso
INSERT INTO usuarios (sucursal_id, username, nombre, password, rol) VALUES
(1, 'admin', 'Administrador',
 '$2y$12$YQHrT45XCeaeUjmgaHUI1ezhI08tYbnfntYNijChdBVYr7qZLx596', 'admin');

INSERT IGNORE INTO config (clave, valor, descripcion) VALUES
('porcentaje_mora_diaria_default', '0.1000', 'Porcentaje de mora diaria por defecto (0.10 = 0.10%)'),
('moneda_simbolo',                 '$',      'Símbolo de moneda'),
('moneda_codigo',                  'ARS',    'Código de moneda ISO');
