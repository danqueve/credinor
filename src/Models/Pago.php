<?php
// src/Models/Pago.php
namespace App\Models;

use App\Core\Model;

class Pago extends Model
{
    protected string $table = 'pagos';

    public function getByCuota(int $cuotaId): array
    {
        return $this->query(
            "SELECT p.*, u.nombre AS cobrador_nombre
             FROM pagos p
             JOIN usuarios u ON p.cobrador_id = u.id
             WHERE p.cuota_id = ? AND p.estado != 'anulado'
             ORDER BY p.created_at ASC",
            [$cuotaId]
        )->fetchAll();
    }

    public function getDelDia(int $cobradorId): array
    {
        return $this->query(
            "SELECT p.*, cu.numero_cuota, cl.nombre AS cliente_nombre,
                    cr.id AS credito_id
             FROM pagos p
             JOIN cuotas cu ON p.cuota_id = cu.id
             JOIN creditos cr ON cu.credito_id = cr.id
             JOIN clientes cl ON cr.cliente_id = cl.id
             WHERE p.cobrador_id = ?
               AND DATE(p.created_at) = CURDATE()
               AND p.estado = 'pendiente_rendir'
             ORDER BY p.created_at DESC",
            [$cobradorId]
        )->fetchAll();
    }
    public function getPagoConDetalles(int $pagoId): ?array
    {
        $row = $this->query(
            "SELECT p.*,
                    cu.numero_cuota, cu.monto AS monto_cuota, cu.fecha_vencimiento,
                    cr.id AS credito_id, cr.frecuencia, cr.cantidad_cuotas,
                    cl.nombre AS cliente_nombre, cl.dni, cl.domicilio,
                    u_cob.nombre AS cobrador_nombre
             FROM pagos p
             JOIN cuotas cu ON p.cuota_id = cu.id
             JOIN creditos cr ON cu.credito_id = cr.id
             JOIN clientes cl ON cr.cliente_id = cl.id
             JOIN usuarios u_cob ON p.cobrador_id = u_cob.id
             WHERE p.id = ?",
            [$pagoId]
        )->fetch();
        return $row ?: null;
    }

    public function getDelCobrador(int $cobradorId, int $dias = 30): array
    {
        return $this->query(
            "SELECT p.*, cu.numero_cuota, cl.nombre AS cliente_nombre,
                    cr.id AS credito_id
             FROM pagos p
             JOIN cuotas cu ON p.cuota_id = cu.id
             JOIN creditos cr ON cu.credito_id = cr.id
             JOIN clientes cl ON cr.cliente_id = cl.id
             WHERE p.cobrador_id = ?
               AND p.created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
               AND p.estado != 'anulado'
             ORDER BY p.created_at DESC",
            [$cobradorId, $dias]
        )->fetchAll();
    }
}
