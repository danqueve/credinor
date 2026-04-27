<?php
// src/Models/Rendicion.php
namespace App\Models;

use App\Core\Model;

class Rendicion extends Model
{
    protected string $table = 'rendiciones';

    public function getPendientesAdmin(): array
    {
        return $this->query(
            "SELECT r.*, u.nombre AS cobrador_nombre, s.nombre AS sucursal_nombre
             FROM rendiciones r
             JOIN usuarios u ON r.cobrador_id = u.id
             JOIN sucursales s ON r.sucursal_id = s.id
             WHERE r.estado = 'pendiente'
             ORDER BY r.fecha DESC"
        )->fetchAll();
    }

    public function getAdminListado(string $estado = ''): array
    {
        $sql = "SELECT r.*, u.nombre AS cobrador_nombre, s.nombre AS sucursal_nombre
                FROM rendiciones r
                JOIN usuarios u ON r.cobrador_id = u.id
                JOIN sucursales s ON r.sucursal_id = s.id";
        $params = [];
        if ($estado) {
            $sql .= " WHERE r.estado = ?";
            $params[] = $estado;
        }
        $sql .= " ORDER BY r.fecha DESC, r.id DESC LIMIT 200";
        return $this->query($sql, $params)->fetchAll();
    }

    public function getTotalesPorEstado(): array
    {
        $rows = $this->query(
            "SELECT estado, COUNT(*) AS cantidad, COALESCE(SUM(monto_declarado), 0) AS monto
             FROM rendiciones GROUP BY estado"
        )->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['estado']] = $row;
        }
        return $result;
    }

    public function getConPagos(int $id): ?array
    {
        $row = $this->query(
            "SELECT r.*, u.nombre AS cobrador_nombre, s.nombre AS sucursal_nombre
             FROM rendiciones r
             JOIN usuarios u ON r.cobrador_id = u.id
             JOIN sucursales s ON r.sucursal_id = s.id
             WHERE r.id = ?",
            [$id]
        )->fetch();

        if (!$row) return null;

        $row['pagos'] = $this->query(
            "SELECT p.*, cu.numero_cuota, cl.nombre AS cliente_nombre,
                    cr.id AS credito_id
             FROM pagos p
             JOIN cuotas cu ON p.cuota_id = cu.id
             JOIN creditos cr ON cu.credito_id = cr.id
             JOIN clientes cl ON cr.cliente_id = cl.id
             WHERE p.rendicion_id = ?
             ORDER BY p.created_at ASC",
            [$id]
        )->fetchAll();

        return $row;
    }

    public function getDelCobradorConPagos(int $id, int $cobradorId): ?array
    {
        $row = $this->query(
            "SELECT r.*, u.nombre AS cobrador_nombre, s.nombre AS sucursal_nombre
             FROM rendiciones r
             JOIN usuarios u ON r.cobrador_id = u.id
             JOIN sucursales s ON r.sucursal_id = s.id
             WHERE r.id = ? AND r.cobrador_id = ?",
            [$id, $cobradorId]
        )->fetch();

        if (!$row) return null;

        $row['pagos'] = $this->query(
            "SELECT p.*, cu.numero_cuota, cl.nombre AS cliente_nombre,
                    cr.id AS credito_id
             FROM pagos p
             JOIN cuotas cu ON p.cuota_id = cu.id
             JOIN creditos cr ON cu.credito_id = cr.id
             JOIN clientes cl ON cr.cliente_id = cl.id
             WHERE p.rendicion_id = ?
             ORDER BY p.created_at ASC",
            [$id]
        )->fetchAll();

        return $row;
    }

    public function getDelCobrador(int $cobradorId, int $limit = 30): array
    {
        return $this->query(
            "SELECT r.*,
                    (SELECT COUNT(*) FROM pagos p WHERE p.rendicion_id = r.id) AS cantidad_pagos
             FROM rendiciones r
             WHERE r.cobrador_id = ?
             ORDER BY r.fecha DESC, r.id DESC
             LIMIT ?",
            [$cobradorId, $limit]
        )->fetchAll();
    }

    public function getDeHoy(int $cobradorId): ?array
    {
        $row = $this->query(
            "SELECT * FROM rendiciones WHERE cobrador_id = ? AND fecha = CURDATE() LIMIT 1",
            [$cobradorId]
        )->fetch();
        return $row ?: null;
    }
}
