<?php
// src/Services/CreditoService.php
namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Helpers\DateHelper;
use App\Helpers\MoneyHelper;
use App\Models\Credito;
use App\Models\Cuota;
use PDO;

class CreditoService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // -------------------------------------------------------
    // CREAR SOLICITUD (vendedor)
    // -------------------------------------------------------
    public function crearSolicitud(array $data): int
    {
        // Validaciones de negocio
        $montoPrestado  = (float) $data['monto_prestado'];
        $montoDevolver  = (float) $data['monto_a_devolver'];
        $cantCuotas     = (int)   $data['cantidad_cuotas'];

        if ($montoDevolver < $montoPrestado) {
            throw new \DomainException('El monto a devolver no puede ser menor al prestado.');
        }
        if ($cantCuotas < 1) {
            throw new \DomainException('Debe haber al menos 1 cuota.');
        }
        if ($montoPrestado <= 0) {
            throw new \DomainException('El monto prestado debe ser mayor a 0.');
        }

        $userId = Auth::id();

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("
                INSERT INTO creditos
                    (sucursal_id, cliente_id, vendedor_id, cobrador_id, garante_id,
                     monto_prestado, monto_a_devolver, cantidad_cuotas,
                     frecuencia, fecha_inicio, fecha_primera_cuota,
                     aplica_mora, porcentaje_mora_diaria, observaciones, estado)
                VALUES
                    (:sucursal_id, :cliente_id, :vendedor_id, :cobrador_id, :garante_id,
                     :monto_prestado, :monto_a_devolver, :cantidad_cuotas,
                     :frecuencia, :fecha_inicio, :fecha_primera_cuota,
                     :aplica_mora, :porcentaje_mora_diaria, :observaciones,
                     'activo')
            ");

            $stmt->execute([
                ':sucursal_id'           => $data['sucursal_id'],
                ':cliente_id'            => $data['cliente_id'],
                ':vendedor_id'           => $userId,
                ':cobrador_id'           => $userId,
                ':garante_id'            => $data['garante_id'] ?: null,
                ':monto_prestado'        => $montoPrestado,
                ':monto_a_devolver'      => $montoDevolver,
                ':cantidad_cuotas'       => $cantCuotas,
                ':frecuencia'            => $data['frecuencia'],
                ':fecha_inicio'          => $data['fecha_inicio'],
                ':fecha_primera_cuota'   => $data['fecha_primera_cuota'],
                ':aplica_mora'           => (int) ($data['aplica_mora'] ?? 0),
                ':porcentaje_mora_diaria'=> $data['porcentaje_mora_diaria'] ?: null,
                ':observaciones'         => $data['observaciones'] ?? null,
            ]);

            $creditoId = (int) $this->db->lastInsertId();

            $credito = (new Credito())->find($creditoId);
            $this->generarCuotas($credito);

            $this->registrarLog($creditoId, null, 'activo', 'Crédito creado y activado por staff.');

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }

        return $creditoId;
    }

    // -------------------------------------------------------
    // AUTORIZAR (admin) — genera cuotas + asigna cobrador
    // -------------------------------------------------------
    public function autorizar(int $creditoId, int $cobradorId): void
    {
        $credito = (new Credito())->findOrFail($creditoId);

        if ($credito['estado'] !== 'pendiente_autorizacion') {
            throw new \DomainException('El crédito no está en estado pendiente de autorización.');
        }

        $this->db->beginTransaction();
        try {
            // 1. Actualizar crédito
            $stmt = $this->db->prepare("
                UPDATE creditos
                SET estado = 'activo', cobrador_id = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$cobradorId, $creditoId]);

            // 2. Generar cuotas
            $this->generarCuotas($credito);

            // 3. Log
            $this->registrarLog($creditoId, 'pendiente_autorizacion', 'activo',
                "Autorizado por admin ID " . Auth::id() . ". Cobrador asignado ID {$cobradorId}.");

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // -------------------------------------------------------
    // RECHAZAR (admin)
    // -------------------------------------------------------
    public function rechazar(int $creditoId, string $motivo): void
    {
        $credito = (new Credito())->findOrFail($creditoId);

        if ($credito['estado'] !== 'pendiente_autorizacion') {
            throw new \DomainException('El crédito no está pendiente de autorización.');
        }

        $stmt = $this->db->prepare("
            UPDATE creditos
            SET estado = 'rechazado', motivo_rechazo = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$motivo, $creditoId]);

        $this->registrarLog($creditoId, 'pendiente_autorizacion', 'rechazado',
            "Rechazado: {$motivo}");
    }

    // -------------------------------------------------------
    // GENERAR CUOTAS
    // -------------------------------------------------------
    private function generarCuotas(array $credito): void
    {
        $fechas = DateHelper::generarFechas(
            $credito['fecha_primera_cuota'],
            $credito['cantidad_cuotas'],
            $credito['frecuencia']
        );

        [$montoCuota, $montoUltima] = MoneyHelper::distribuirCuotas(
            (float) $credito['monto_a_devolver'],
            (int)   $credito['cantidad_cuotas']
        );

        $stmt = $this->db->prepare("
            INSERT INTO cuotas (credito_id, numero_cuota, monto, fecha_vencimiento, estado)
            VALUES (?, ?, ?, ?, 'pendiente')
        ");

        $total = count($fechas);
        foreach ($fechas as $i => $fecha) {
            $numeroCuota = $i + 1;
            $monto = ($numeroCuota === $total) ? $montoUltima : $montoCuota;
            $stmt->execute([$credito['id'], $numeroCuota, $monto, $fecha]);
        }
    }

    // -------------------------------------------------------
    // VERIFICAR FINALIZACIÓN AUTOMÁTICA
    // -------------------------------------------------------
    public function verificarFinalizacion(int $creditoId): bool
    {
        $row = $this->db->prepare("
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN estado = 'pagada' THEN 1 ELSE 0 END) AS pagadas
            FROM cuotas WHERE credito_id = ?
        ");
        $row->execute([$creditoId]);
        $data = $row->fetch();

        if ((int)$data['total'] > 0 && (int)$data['total'] === (int)$data['pagadas']) {
            $credito = (new Credito())->find($creditoId);
            if ($credito && (float)$credito['mora_acumulada'] <= (float)$credito['mora_pagada']) {
                $this->db->prepare("
                    UPDATE creditos SET estado = 'finalizado', updated_at = NOW() WHERE id = ?
                ")->execute([$creditoId]);
                $this->registrarLog($creditoId, 'activo', 'finalizado', 'Crédito finalizado automáticamente.');
                return true;
            }
        }
        return false;
    }

    // -------------------------------------------------------
    // LOG DE AUDITORÍA
    // -------------------------------------------------------
    private function registrarLog(
        int $creditoId,
        ?string $desde,
        string $hasta,
        string $nota
    ): void {
        $stmt = $this->db->prepare("
            INSERT INTO creditos_log (credito_id, usuario_id, estado_desde, estado_hasta, nota)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$creditoId, Auth::id() ?? 1, $desde, $hasta, $nota]);
    }
}
