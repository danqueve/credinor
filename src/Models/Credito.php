<?php
// src/Models/Credito.php
namespace App\Models;

use App\Core\Model;

class Credito extends Model
{
    protected string $table = 'creditos';

    public function getPendientes(): array
    {
        return $this->query(
            "SELECT c.*, cl.nombre AS cliente_nombre, cl.dni, s.nombre AS sucursal_nombre,
                    u.nombre AS vendedor_nombre
             FROM creditos c
             JOIN clientes cl ON c.cliente_id = cl.id
             JOIN sucursales s ON c.sucursal_id = s.id
             JOIN usuarios u ON c.vendedor_id = u.id
             WHERE c.estado = 'pendiente_autorizacion'
             ORDER BY c.created_at ASC"
        )->fetchAll();
    }

    public function countPendientes(): int
    {
        return (int) $this->query(
            "SELECT COUNT(*) FROM creditos WHERE estado = 'pendiente_autorizacion'"
        )->fetchColumn();
    }

    public function countActivos(): int
    {
        return (int) $this->query(
            "SELECT COUNT(*) FROM creditos WHERE estado = 'activo'"
        )->fetchColumn();
    }

    public function countPendientesBySucursal(int $sucursalId): int
    {
        return (int) $this->query(
            "SELECT COUNT(*) FROM creditos WHERE estado = 'pendiente_autorizacion' AND sucursal_id = ?",
            [$sucursalId]
        )->fetchColumn();
    }

    public function countActivosBySucursal(int $sucursalId): int
    {
        return (int) $this->query(
            "SELECT COUNT(*) FROM creditos WHERE estado = 'activo' AND sucursal_id = ?",
            [$sucursalId]
        )->fetchColumn();
    }

    public function sumMoraTotal(): float
    {
        return (float) $this->query(
            "SELECT COALESCE(SUM(mora_acumulada), 0) FROM creditos WHERE estado = 'activo'"
        )->fetchColumn();
    }

    public function getConDetalles(int $id): ?array
    {
        $row = $this->query(
            "SELECT c.*, cl.nombre AS cliente_nombre, cl.dni, cl.telefono, cl.domicilio,
                    cl.lat, cl.lng, s.nombre AS sucursal_nombre,
                    u_vend.nombre AS vendedor_nombre,
                    u_cob.nombre AS cobrador_nombre
             FROM creditos c
             JOIN clientes cl ON c.cliente_id = cl.id
             JOIN sucursales s ON c.sucursal_id = s.id
             JOIN usuarios u_vend ON c.vendedor_id = u_vend.id
             LEFT JOIN usuarios u_cob ON c.cobrador_id = u_cob.id
             WHERE c.id = ?",
            [$id]
        )->fetch();
        return $row ?: null;
    }

    public function getByCliente(int $clienteId): array
    {
        return $this->query(
            "SELECT c.*, u.nombre AS cobrador_nombre
             FROM creditos c
             LEFT JOIN usuarios u ON c.cobrador_id = u.id
             WHERE c.cliente_id = ?
             ORDER BY c.created_at DESC",
            [$clienteId]
        )->fetchAll();
    }

    public function getBySucursal(int $sucursalId, string $estado = ''): array
    {
        $sql = "SELECT c.*, cl.nombre AS cliente_nombre, cl.dni
                FROM creditos c
                JOIN clientes cl ON c.cliente_id = cl.id
                WHERE c.sucursal_id = ?";
        $params = [$sucursalId];
        if ($estado) {
            $sql .= ' AND c.estado = ?';
            $params[] = $estado;
        }
        $sql .= ' ORDER BY c.created_at DESC LIMIT 100';
        return $this->query($sql, $params)->fetchAll();
    }

    public function getAdminListado(string $estado = ''): array
    {
        $sql = "SELECT c.*, cl.nombre AS cliente_nombre, cl.dni,
                       s.nombre AS sucursal_nombre, u.nombre AS vendedor_nombre
                FROM creditos c
                JOIN clientes cl ON c.cliente_id = cl.id
                JOIN sucursales s ON c.sucursal_id = s.id
                JOIN usuarios u ON c.vendedor_id = u.id";
        $params = [];
        if ($estado) {
            $sql .= ' WHERE c.estado = ?';
            $params[] = $estado;
        }
        $sql .= ' ORDER BY c.created_at DESC LIMIT 200';
        return $this->query($sql, $params)->fetchAll();
    }

    public function getLog(int $creditoId): array
    {
        return $this->query(
            "SELECT l.*, u.nombre AS usuario_nombre
             FROM creditos_log l
             JOIN usuarios u ON l.usuario_id = u.id
             WHERE l.credito_id = ?
             ORDER BY l.created_at ASC",
            [$creditoId]
        )->fetchAll();
    }
}
