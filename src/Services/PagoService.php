<?php
// src/Services/PagoService.php
namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Models\Credito;
use App\Models\Cuota;
use PDO;

class PagoService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Registra un pago (total o parcial).
     * Imputación: capital primero, mora aparte.
     *
     * @param  int   $cuotaId
     * @param  float $montoIngresado  Monto que entrega el cliente
     * @param  float $montoAMora      Porción que el cobrador destina a mora (default 0)
     * @return int   ID del pago creado
     */
    public function registrar(int $cuotaId, float $montoIngresado, float $montoAMora = 0.0, string $metodoPago = 'efectivo'): int
    {
        if ($montoIngresado <= 0) {
            throw new \DomainException('El monto debe ser mayor a cero.');
        }

        $this->db->beginTransaction();
        try {
            // --- Datos de la cuota ---
            $cuota = $this->db->prepare(
                'SELECT cu.*, cr.id AS credito_id, cr.mora_acumulada, cr.mora_pagada,
                        cr.cobrador_id, cr.estado AS credito_estado
                 FROM cuotas cu
                 JOIN creditos cr ON cu.credito_id = cr.id
                 WHERE cu.id = ?'
            );
            $cuota->execute([$cuotaId]);
            $cu = $cuota->fetch();

            if (!$cu) throw new \DomainException('Cuota no encontrada.');
            if ($cu['credito_estado'] !== 'activo') {
                throw new \DomainException('El crédito no está activo.');
            }

            // Saldo real de la cuota
            $saldoPago = $this->db->prepare(
                "SELECT COALESCE(SUM(monto), 0) FROM pagos
                 WHERE cuota_id = ? AND estado != 'anulado'"
            );
            $saldoPago->execute([$cuotaId]);
            $yaPageado = (float) $saldoPago->fetchColumn();
            $saldoCuota = (float)$cu['monto'] - $yaPageado;

            if ($saldoCuota <= 0) {
                throw new \DomainException('La cuota ya está pagada.');
            }

            // Validar distribución
            $montoAMora    = max(0.0, min($montoAMora, $montoIngresado));
            $montoACapital = $montoIngresado - $montoAMora;

            // Insertar pago
            $stmt = $this->db->prepare("
                INSERT INTO pagos
                    (cuota_id, cobrador_id, monto, monto_a_capital, monto_a_mora, metodo_pago, estado)
                VALUES (?, ?, ?, ?, ?, ?, 'pendiente_rendir')
            ");
            $stmt->execute([
                $cuotaId,
                Auth::id(),
                $montoIngresado,
                $montoACapital,
                $montoAMora,
                $metodoPago
            ]);
            $pagoId = (int) $this->db->lastInsertId();

            // Actualizar mora pagada en el crédito
            if ($montoAMora > 0) {
                $this->db->prepare("
                    UPDATE creditos
                    SET mora_pagada = mora_pagada + ?, updated_at = NOW()
                    WHERE id = ?
                ")->execute([$montoAMora, $cu['credito_id']]);
            }

            // Determinar nuevo estado de la cuota
            $totalPagadoAhora = $yaPageado + $montoACapital;
            if ($totalPagadoAhora >= (float)$cu['monto'] - 0.01) {
                $nuevoEstado = 'pagada';
            } elseif ($totalPagadoAhora > 0) {
                $nuevoEstado = 'parcial';
            } else {
                $nuevoEstado = $cu['estado'];
            }

            $this->db->prepare("
                UPDATE cuotas SET estado = ?, updated_at = NOW() WHERE id = ?
            ")->execute([$nuevoEstado, $cuotaId]);

            $this->db->commit();

            // Verificar si el crédito se finalizó
            (new CreditoService())->verificarFinalizacion($cu['credito_id']);

            return $pagoId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Anular un pago y revertir el estado de la cuota.
     */
    public function anular(int $pagoId): void
    {
        $pago = $this->db->prepare(
            "SELECT * FROM pagos WHERE id = ? AND estado != 'anulado'"
        );
        $pago->execute([$pagoId]);
        $p = $pago->fetch();
        if (!$p) throw new \DomainException('Pago no encontrado o ya anulado.');

        $this->db->beginTransaction();
        try {
            // Marcar pago como anulado
            $this->db->prepare("
                UPDATE pagos SET estado = 'anulado', updated_at = NOW() WHERE id = ?
            ")->execute([$pagoId]);

            // Revertir mora_pagada
            if ((float)$p['monto_a_mora'] > 0) {
                $this->db->prepare("
                    UPDATE creditos
                    SET mora_pagada = GREATEST(0, mora_pagada - ?), updated_at = NOW()
                    WHERE id = (SELECT credito_id FROM cuotas WHERE id = ?)
                ")->execute([$p['monto_a_mora'], $p['cuota_id']]);
            }

            // Recalcular estado de la cuota
            $this->recalcularEstadoCuota((int)$p['cuota_id']);

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function recalcularEstadoCuota(int $cuotaId): void
    {
        $row = $this->db->prepare(
            "SELECT cu.monto,
                    COALESCE(SUM(p.monto_a_capital), 0) AS pagado,
                    cu.fecha_vencimiento
             FROM cuotas cu
             LEFT JOIN pagos p ON p.cuota_id = cu.id AND p.estado != 'anulado'
             WHERE cu.id = ?
             GROUP BY cu.id"
        );
        $row->execute([$cuotaId]);
        $cu = $row->fetch();

        if ($cu['pagado'] >= (float)$cu['monto'] - 0.01) {
            $estado = 'pagada';
        } elseif ($cu['pagado'] > 0) {
            $estado = 'parcial';
        } elseif ($cu['fecha_vencimiento'] < date('Y-m-d')) {
            $estado = 'vencida';
        } else {
            $estado = 'pendiente';
        }

        $this->db->prepare("UPDATE cuotas SET estado = ? WHERE id = ?")
                 ->execute([$estado, $cuotaId]);
    }
}
