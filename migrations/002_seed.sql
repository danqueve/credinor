-- ============================================================
-- 002_seed.sql — Datos iniciales del sistema
-- Ejecutar DESPUÉS de 001_schema.sql
-- ============================================================

-- Sucursal principal
INSERT INTO sucursales (nombre, direccion, telefono) VALUES
('Casa Central', 'Av. Principal 123', '299-4000000');

-- Admin inicial (usuario: admin / password: Admin1234! — cambiarlo al ingresar)
-- Hash bcrypt de 'Admin1234!'
INSERT INTO usuarios (sucursal_id, username, nombre, password, rol) VALUES
(1, 'admin', 'Administrador',
 '$2y$12$YQHrT45XCeaeUjmgaHUI1ezhI08tYbnfntYNijChdBVYr7qZLx596', 'admin');

-- Config global de mora
INSERT INTO config (clave, valor, descripcion) VALUES
('porcentaje_mora_diaria_default', '0.1000', 'Porcentaje de mora diaria por defecto (0.10 = 0.10%)'),
('moneda_simbolo', '$', 'Símbolo de moneda'),
('moneda_codigo', 'ARS', 'Código de moneda ISO');
