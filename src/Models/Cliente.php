<?php
// src/Models/Cliente.php
namespace App\Models;

use App\Core\Model;

class Cliente extends Model
{
    protected string $table = 'clientes';

    public function findByDni(string $dni): ?array
    {
        $row = $this->query(
            'SELECT * FROM clientes WHERE dni = ? LIMIT 1',
            [$dni]
        )->fetch();
        return $row ?: null;
    }

    public function searchBySucursal(int $sucursalId, string $q = ''): array
    {
        if ($q) {
            return $this->query(
                "SELECT * FROM clientes
                 WHERE sucursal_id = ? AND (nombre LIKE ? OR dni LIKE ?)
                 ORDER BY nombre LIMIT 50",
                [$sucursalId, "%{$q}%", "%{$q}%"]
            )->fetchAll();
        }
        return $this->query(
            'SELECT * FROM clientes WHERE sucursal_id = ? ORDER BY nombre LIMIT 100',
            [$sucursalId]
        )->fetchAll();
    }

    public function searchAdmin(string $q = ''): array
    {
        if ($q) {
            return $this->query(
                "SELECT cl.*, s.nombre AS sucursal_nombre
                 FROM clientes cl
                 JOIN sucursales s ON cl.sucursal_id = s.id
                 WHERE cl.nombre LIKE ? OR cl.dni LIKE ?
                 ORDER BY cl.nombre LIMIT 100",
                ["%{$q}%", "%{$q}%"]
            )->fetchAll();
        }
        return $this->query(
            "SELECT cl.*, s.nombre AS sucursal_nombre
             FROM clientes cl
             JOIN sucursales s ON cl.sucursal_id = s.id
             ORDER BY cl.nombre LIMIT 100"
        )->fetchAll();
    }
}
