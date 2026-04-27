<?php
// src/Services/RendicionService.php
namespace App\Services;

use App\Core\Database;
use PDO;

class RendicionService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Cobrador cierra su caja del día.
     * Agrupa todos los pagos pendiente_rendir del día y crea la rendición.
     */
    public function cerrar(int $cobradorId, int $sucursalId): int
    {
        // Verificar que no haya cerrado ya hoy
        $yaExiste = $this->db->prepare(
            "SELECT id FROM rendiciones
             WHERE cobrador_id = ? AND fecha = CURDATE() AND estado != 'rechazada'"
        );
        $yaExiste->execute([$cobradorId]);
        if ($yaExiste->fetch()) {
            throw new \DomainException('Ya cerraste la caja de hoy.');
        }

        // Sumar pagos del día
        $stmtTotal = $this->db->prepare("
            SELECT COALESCE(SUM(monto), 0)
            FROM pagos
            WHERE cobrador_id = ? AND DATE(created_at) = CURDATE() AND estado = 'pendiente_rendir'
        ");
        $stmtTotal->execute([$cobradorId]);
        $total = (float) $stmtTotal->fetchColumn();

        if ($total <= 0) {
            throw new \DomainException('No hay pagos pendientes de rendir para hoy.');
        }

        $this->db->beginTransaction();
        try {
            // Crear rendición
            $stmt = $this->db->prepare("
                INSERT INTO rendiciones (cobrador_id, sucursal_id, fecha, monto_declarado, estado)
                VALUES (?, ?, CURDATE(), ?, 'pendiente')
            ");
            $stmt->execute([$cobradorId, $sucursalId, $total]);
            $rendicionId = (int) $this->db->lastInsertId();

            // Vincular pagos a la rendición
            $this->db->prepare("
                UPDATE pagos
                SET rendicion_id = ?, estado = 'rendido', updated_at = NOW()
                WHERE cobrador_id = ? AND DATE(created_at) = CURDATE() AND estado = 'pendiente_rendir'
            ")->execute([$rendicionId, $cobradorId]);

            $this->db->commit();
            return $rendicionId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Admin confirma una rendición.
     */
    public function confirmar(int $rendicionId, float $montoRecibido, int $adminId): void
    {
        $this->db->beginTransaction();
        try {
            $this->db->prepare("
                UPDATE rendiciones
                SET estado = 'confirmada',
                    monto_recibido = ?,
                    admin_id = ?,
                    confirmado_at = NOW(),
                    updated_at = NOW()
                WHERE id = ?
            ")->execute([$montoRecibido, $adminId, $rendicionId]);

            // Confirmar pagos
            $this->db->prepare("
                UPDATE pagos
                SET estado = 'confirmado', updated_at = NOW()
                WHERE rendicion_id = ?
            ")->execute([$rendicionId]);

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
