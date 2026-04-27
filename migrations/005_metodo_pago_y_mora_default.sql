-- migrations/005_metodo_pago_y_mora_default.sql
-- Cambios: agrega metodo_pago a pagos, cambia aplica_mora default a 0.

ALTER TABLE pagos
    ADD COLUMN metodo_pago ENUM('efectivo','transferencia') NOT NULL DEFAULT 'efectivo' AFTER monto_a_mora;

ALTER TABLE creditos
    ALTER COLUMN aplica_mora SET DEFAULT 0;

UPDATE creditos SET aplica_mora = 0;
