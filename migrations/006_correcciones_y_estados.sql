-- =============================================================
-- Migration 006 — Correcciones operativas y estados extendidos
-- =============================================================
-- Run: mysql -u root credinor < migrations/006_correcciones_y_estados.sql
-- =============================================================

-- 1. Auditoría de anulación en pagos
ALTER TABLE pagos
    ADD COLUMN anulado_at       DATETIME        NULL        AFTER estado,
    ADD COLUMN anulado_por      INT UNSIGNED    NULL        AFTER anulado_at,
    ADD COLUMN motivo_anulacion VARCHAR(255)    NULL        AFTER anulado_por;

-- 2. Estado "cancelada" para cuotas
ALTER TABLE cuotas
    MODIFY estado ENUM('pendiente','parcial','pagada','vencida','cancelada')
                  NOT NULL DEFAULT 'pendiente';
