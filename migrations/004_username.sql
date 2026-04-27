-- 004_username.sql
-- Reemplaza el campo email por username en la tabla usuarios

ALTER TABLE usuarios
    ADD COLUMN username VARCHAR(50) NOT NULL DEFAULT '' AFTER sucursal_id,
    ADD UNIQUE KEY uq_usuarios_username (username);

-- Poblar username con la parte antes del @ del email existente (como punto de partida)
UPDATE usuarios SET username = SUBSTRING_INDEX(email, '@', 1) WHERE username = '';

-- Eliminar columna email
ALTER TABLE usuarios DROP COLUMN email;

-- Actualizar el usuario admin inicial al nombre de usuario 'admin'
UPDATE usuarios SET username = 'admin' WHERE rol = 'admin' LIMIT 1;
