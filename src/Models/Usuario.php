<?php
// src/Models/Usuario.php
namespace App\Models;

use App\Core\Model;

class Usuario extends Model
{
    protected string $table = 'usuarios';

    public function findByUsername(string $username): ?array
    {
        $row = $this->query(
            'SELECT * FROM usuarios WHERE username = ? LIMIT 1',
            [$username]
        )->fetch();
        return $row ?: null;
    }

    public function getBySucursal(int $sucursalId): array
    {
        return $this->query(
            'SELECT * FROM usuarios WHERE sucursal_id = ? AND activo = 1 ORDER BY nombre',
            [$sucursalId]
        )->fetchAll();
    }

    public function getCobradores(?int $sucursalId = null): array
    {
        if ($sucursalId) {
            return $this->query(
                "SELECT * FROM usuarios WHERE rol = 'cobrador' AND sucursal_id = ? AND activo = 1 ORDER BY nombre",
                [$sucursalId]
            )->fetchAll();
        }
        return $this->query(
            "SELECT * FROM usuarios WHERE rol = 'cobrador' AND activo = 1 ORDER BY nombre"
        )->fetchAll();
    }
    public function getConSucursal(): array
    {
        return $this->query(
            "SELECT u.*, s.nombre AS sucursal_nombre
             FROM usuarios u
             LEFT JOIN sucursales s ON u.sucursal_id = s.id
             ORDER BY u.rol ASC, u.nombre ASC"
        )->fetchAll();
    }
}
