<?php
// src/Models/Cuota.php
namespace App\Models;

use App\Core\Model;

class Cuota extends Model
{
    protected string $table = 'cuotas';

    public function getAgendaHoy(int $cobradorId): array
    {
        return $this->query(
            "SELECT cu.*, cl.nombre AS cliente_nombre, cl.telefono, cl.domicilio,
                    cl.lat, cl.lng,
                    cr.monto_prestado, cr.mora_acumulada, cr.id AS credito_id,
                    (cu.monto - COALESCE(
                        (SELECT SUM(monto) FROM pagos WHERE cuota_id = cu.id AND estado != 'anulado'), 0
                    )) AS saldo
             FROM cuotas cu
             JOIN creditos cr ON cu.credito_id = cr.id
             JOIN clientes cl ON cr.cliente_id = cl.id
             WHERE cr.cobrador_id = ?
               AND cu.fecha_vencimiento = CURDATE()
               AND cu.estado IN ('pendiente', 'parcial')
               AND cr.estado = 'activo'
             ORDER BY cl.nombre ASC",
            [$cobradorId]
        )->fetchAll();
    }

    public function getAgendaVencida(int $cobradorId): array
    {
        return $this->query(
            "SELECT cu.*, cl.nombre AS cliente_nombre, cl.telefono, cl.domicilio,
                    cl.lat, cl.lng,
                    cr.mora_acumulada, cr.id AS credito_id,
                    (cu.monto - COALESCE(
                        (SELECT SUM(monto) FROM pagos WHERE cuota_id = cu.id AND estado != 'anulado'), 0
                    )) AS saldo
             FROM cuotas cu
             JOIN creditos cr ON cu.credito_id = cr.id
             JOIN clientes cl ON cr.cliente_id = cl.id
             WHERE cr.cobrador_id = ?
               AND cu.fecha_vencimiento < CURDATE()
               AND cu.estado IN ('pendiente', 'parcial', 'vencida')
               AND cr.estado = 'activo'
             ORDER BY cu.fecha_vencimiento ASC, cl.nombre ASC",
            [$cobradorId]
        )->fetchAll();
    }

    public function totalCobradoHoy(int $cobradorId): float
    {
        return (float) $this->query(
            "SELECT COALESCE(SUM(p.monto), 0)
             FROM pagos p
             JOIN cuotas cu ON p.cuota_id = cu.id
             JOIN creditos cr ON cu.credito_id = cr.id
             WHERE cr.cobrador_id = ?
               AND DATE(p.created_at) = CURDATE()
               AND p.estado != 'anulado'",
            [$cobradorId]
        )->fetchColumn();
    }

    public function getByCreditoOrdenadas(int $creditoId): array
    {
        return $this->query(
            "SELECT cu.*,
                    COALESCE(SUM(p.monto), 0) AS monto_pagado
             FROM cuotas cu
             LEFT JOIN pagos p ON p.cuota_id = cu.id AND p.estado != 'anulado'
             WHERE cu.credito_id = ?
             GROUP BY cu.id
             ORDER BY cu.numero_cuota ASC",
            [$creditoId]
        )->fetchAll();
    }
    public function getAgendaFutura(int $cobradorId, int $dias = 7): array
    {
        return $this->query(
            "SELECT cu.*, cl.nombre AS cliente_nombre, cl.domicilio,
                    cr.id AS credito_id
             FROM cuotas cu
             JOIN creditos cr ON cu.credito_id = cr.id
             JOIN clientes cl ON cr.cliente_id = cl.id
             WHERE cr.cobrador_id = ?
               AND cu.fecha_vencimiento > CURDATE()
               AND cu.fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
               AND cu.estado = 'pendiente'
               AND cr.estado = 'activo'
             ORDER BY cu.fecha_vencimiento ASC, cl.nombre ASC",
            [$cobradorId, $dias]
        )->fetchAll();
    }
}
