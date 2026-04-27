<?php
// src/Services/MoraService.php
namespace App\Services;

use App\Core\Database;
use PDO;

class MoraService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Devenga mora diaria para TODAS las cuotas vencidas con saldo > 0.
     * Es idempotente: la unique key (cuota_id, fecha) evita doble cargo.
     *
     * @param  string $fecha  Fecha a procesar (default: hoy)
     * @return array  Resumen del proceso
     */
    public function devengarDia(string $fecha = ''): array
    {
        if (!$fecha) $fecha = date('Y-m-d');

        // Porcentaje global de mora
        $configStmt = $this->db->prepare(
            "SELECT valor FROM config WHERE clave = 'porcentaje_mora_diaria_default'"
        );
        $configStmt->execute();
        $porcentajeGlobal = (float) ($configStmt->fetchColumn() ?: 0.1);

        // Cuotas vencidas con saldo pendiente
        $cuotas = $this->db->prepare("
            SELECT
                cu.id AS cuota_id,
                cu.monto,
                COALESCE(SUM(p.monto_a_capital), 0) AS monto_pagado,
                cr.id AS credito_id,
                cr.porcentaje_mora_diaria,
                cr.aplica_mora
            FROM cuotas cu
            JOIN creditos cr ON cu.credito_id = cr.id
            LEFT JOIN pagos p ON p.cuota_id = cu.id AND p.estado != 'anulado'
            WHERE cu.fecha_vencimiento < ?
              AND cu.estado IN ('pendiente', 'parcial', 'vencida')
              AND cr.estado = 'activo'
              AND cr.aplica_mora = 1
            GROUP BY cu.id, cr.id, cr.porcentaje_mora_diaria, cr.aplica_mora
            HAVING (cu.monto - COALESCE(SUM(p.monto_a_capital), 0)) > 0.01
        ");
        $cuotas->execute([$fecha]);
        $rows = $cuotas->fetchAll();

        $procesadas  = 0;
        $omitidas    = 0;
        $totalMora   = 0.0;

        $insertMora = $this->db->prepare("
            INSERT IGNORE INTO mora_devengada
                (cuota_id, fecha, saldo_base, porcentaje, monto_mora)
            VALUES (?, ?, ?, ?, ?)
        ");

        $updateCredito = $this->db->prepare("
            UPDATE creditos
            SET mora_acumulada = mora_acumulada + ?, updated_at = NOW()
            WHERE id = ?
        ");

        $marcarVencida = $this->db->prepare("
            UPDATE cuotas SET estado = 'vencida', updated_at = NOW()
            WHERE id = ? AND estado IN ('pendiente', 'parcial')
        ");

        foreach ($rows as $cu) {
            $saldo      = (float)$cu['monto'] - (float)$cu['monto_pagado'];
            $porcentaje = $cu['porcentaje_mora_diaria']
                ? (float)$cu['porcentaje_mora_diaria']
                : $porcentajeGlobal;
            $moraDia    = round($saldo * $porcentaje / 100, 2);

            // INSERT IGNORE: no hace nada si ya existe (cuota_id, fecha)
            $insertMora->execute([
                $cu['cuota_id'],
                $fecha,
                $saldo,
                $porcentaje,
                $moraDia,
            ]);

            if ($insertMora->rowCount() > 0) {
                $updateCredito->execute([$moraDia, $cu['credito_id']]);
                $marcarVencida->execute([$cu['cuota_id']]);
                $totalMora += $moraDia;
                $procesadas++;
            } else {
                $omitidas++;
            }
        }

        return [
            'fecha'      => $fecha,
            'procesadas' => $procesadas,
            'omitidas'   => $omitidas,
            'mora_total' => $totalMora,
        ];
    }

    /**
     * Actualiza estado de cuotas: marca como 'vencida' las que pasaron su fecha
     * sin estar pagadas. Se corre desde el cron actualizar_estados.php
     */
    public function actualizarEstadosCuotas(): int
    {
        $stmt = $this->db->prepare("
            UPDATE cuotas
            SET estado = 'vencida', updated_at = NOW()
            WHERE fecha_vencimiento < CURDATE()
              AND estado = 'pendiente'
              AND credito_id IN (
                  SELECT id FROM creditos WHERE estado = 'activo'
              )
        ");
        $stmt->execute();
        return $stmt->rowCount();
    }
}
