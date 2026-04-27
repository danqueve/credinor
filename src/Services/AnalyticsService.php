<?php
// src/Services/AnalyticsService.php
namespace App\Services;

use App\Core\Database;
use PDO;

class AnalyticsService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Cobranza diaria de los últimos 30 días (excluyendo anulados). */
    public function cobranzaUltimos30Dias(): array
    {
        $rows = $this->db->query(
            "SELECT DATE(created_at) AS fecha,
                    COALESCE(SUM(monto), 0) AS total
             FROM pagos
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
               AND estado != 'anulado'
             GROUP BY DATE(created_at)
             ORDER BY fecha ASC"
        )->fetchAll();

        // Fill gaps with 0
        $result = [];
        $byDate = array_column($rows, 'total', 'fecha');
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $result[] = [
                'fecha' => date('d/m', strtotime($date)),
                'total' => (float)($byDate[$date] ?? 0),
            ];
        }
        return $result;
    }

    /** Mora pendiente por sucursal. */
    public function moraPorSucursal(): array
    {
        return $this->db->query(
            "SELECT s.nombre AS sucursal,
                    COALESCE(SUM(cr.mora_acumulada - cr.mora_pagada), 0) AS mora,
                    COUNT(cr.id) AS creditos
             FROM creditos cr
             JOIN sucursales s ON cr.sucursal_id = s.id
             WHERE cr.estado = 'activo'
               AND cr.mora_acumulada > cr.mora_pagada
             GROUP BY s.id, s.nombre
             ORDER BY mora DESC"
        )->fetchAll();
    }

    /** Top 10 clientes con mayor deuda pendiente. */
    public function top10Deudores(): array
    {
        return $this->db->query(
            "SELECT cl.nombre AS cliente,
                    cl.id AS cliente_id,
                    cr.id AS credito_id,
                    COALESCE(SUM(cu.monto) - COALESCE(SUM(p.monto_a_capital), 0), 0) AS saldo_capital,
                    COALESCE(cr.mora_acumulada - cr.mora_pagada, 0) AS mora_pendiente
             FROM creditos cr
             JOIN clientes cl ON cr.cliente_id = cl.id
             JOIN cuotas cu ON cu.credito_id = cr.id AND cu.estado IN ('pendiente','parcial','vencida')
             LEFT JOIN pagos p ON p.cuota_id = cu.id AND p.estado != 'anulado'
             WHERE cr.estado = 'activo'
             GROUP BY cl.id, cl.nombre, cr.id, cr.mora_acumulada, cr.mora_pagada
             ORDER BY (saldo_capital + mora_pendiente) DESC
             LIMIT 10"
        )->fetchAll();
    }

    /** Embudo de rendiciones de los últimos N días. */
    public function embudoRendiciones(int $dias = 30): array
    {
        $row = $this->db->prepare(
            "SELECT
                COALESCE(SUM(monto_declarado), 0) AS declarado,
                COALESCE(SUM(CASE WHEN estado = 'confirmada' THEN monto_recibido ELSE 0 END), 0) AS recibido,
                COUNT(CASE WHEN estado = 'pendiente'   THEN 1 END) AS pendientes,
                COUNT(CASE WHEN estado = 'confirmada'  THEN 1 END) AS confirmadas,
                COUNT(CASE WHEN estado = 'rechazada'   THEN 1 END) AS rechazadas
             FROM rendiciones
             WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL ? DAY)"
        );
        $row->execute([$dias]);
        return $row->fetch() ?: [
            'declarado' => 0, 'recibido' => 0,
            'pendientes' => 0, 'confirmadas' => 0, 'rechazadas' => 0,
        ];
    }

    /** KPI de cobranza del mes actual vs mes anterior. */
    public function tendenciaMensual(): array
    {
        $mesActual = (float) $this->db->query(
            "SELECT COALESCE(SUM(monto), 0) FROM pagos
             WHERE YEAR(created_at) = YEAR(CURDATE())
               AND MONTH(created_at) = MONTH(CURDATE())
               AND estado != 'anulado'"
        )->fetchColumn();

        $mesAnterior = (float) $this->db->query(
            "SELECT COALESCE(SUM(monto), 0) FROM pagos
             WHERE created_at >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01')
               AND created_at <  DATE_FORMAT(CURDATE(), '%Y-%m-01')
               AND estado != 'anulado'"
        )->fetchColumn();

        $delta = $mesAnterior > 0
            ? round(($mesActual - $mesAnterior) / $mesAnterior * 100, 1)
            : null;

        return [
            'mes_actual'   => $mesActual,
            'mes_anterior' => $mesAnterior,
            'delta'        => $delta,
        ];
    }
}
