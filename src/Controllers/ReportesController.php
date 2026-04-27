<?php
// src/Controllers/ReportesController.php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\MoneyHelper;
use PDO;

class ReportesController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // GET /admin/reportes  → redirige a cartera (entrada del sidebar)
    public function index(): void
    {
        $this->requireRole('admin');
        Response::redirect('/admin/reportes/cartera');
    }

    // ——— CARTERA ACTIVA ————————————————————————————————————
    // GET /admin/reportes/cartera
    public function cartera(): void
    {
        $this->requireRole('admin');

        $filtros = [
            'sucursal_id' => (int) Request::get('sucursal_id', 0),
            'cobrador_id' => (int) Request::get('cobrador_id', 0),
            'frecuencia'  => Request::get('frecuencia', ''),
            'desde'       => Request::get('desde', date('Y-m-01')),
            'hasta'       => Request::get('hasta', date('Y-m-d')),
        ];

        $creditos = $this->getCartera($filtros);
        $resumen  = $this->resumenCartera($creditos);
        $sucursales = $this->getSucursales();
        $cobradores = $this->getCobradores();

        // Export CSV?
        if (Request::get('export') === 'csv') {
            $this->exportCsvCartera($creditos);
            return;
        }

        $this->view('admin/reportes/cartera', compact(
            'creditos', 'resumen', 'filtros', 'sucursales', 'cobradores'
        ));
    }

    // ——— MORA ——————————————————————————————————————————————
    // GET /admin/reportes/mora
    public function mora(): void
    {
        $this->requireRole('admin');

        $topDeudores = $this->db->query(
            "SELECT cl.nombre, cl.dni, cr.id AS credito_id,
                    cr.mora_acumulada, cr.mora_pagada,
                    (cr.mora_acumulada - cr.mora_pagada) AS mora_pendiente,
                    u.nombre AS cobrador_nombre
             FROM creditos cr
             JOIN clientes cl ON cr.cliente_id = cl.id
             LEFT JOIN usuarios u ON cr.cobrador_id = u.id
             WHERE cr.estado = 'activo' AND cr.mora_acumulada > 0
             ORDER BY mora_pendiente DESC
             LIMIT 50"
        )->fetchAll();

        $porCobrador = $this->db->query(
            "SELECT u.nombre AS cobrador_nombre,
                    COUNT(cr.id) AS total_creditos,
                    SUM(cr.mora_acumulada) AS mora_total,
                    SUM(cr.mora_pagada) AS mora_cobrada,
                    SUM(cr.mora_acumulada - cr.mora_pagada) AS mora_pendiente
             FROM creditos cr
             JOIN usuarios u ON cr.cobrador_id = u.id
             WHERE cr.estado = 'activo'
             GROUP BY cr.cobrador_id, u.nombre
             ORDER BY mora_pendiente DESC"
        )->fetchAll();

        $totalMora = array_sum(array_column($topDeudores, 'mora_acumulada'));
        $totalPend = array_sum(array_column($topDeudores, 'mora_pendiente'));

        $this->view('admin/reportes/mora', compact(
            'topDeudores', 'porCobrador', 'totalMora', 'totalPend'
        ));
    }

    // ——— COBRADORES ————————————————————————————————————————
    // GET /admin/reportes/cobradores
    public function cobradores(): void
    {
        $this->requireRole('admin');

        $periodo = Request::get('periodo', 'semana');
        $desde   = match($periodo) {
            'hoy'     => date('Y-m-d'),
            'semana'  => date('Y-m-d', strtotime('monday this week')),
            'mes'     => date('Y-m-01'),
            default   => date('Y-m-d', strtotime('monday this week')),
        };

        $stats = $this->db->prepare(
            "SELECT u.nombre AS cobrador_nombre,
                    u.id AS cobrador_id,
                    COUNT(DISTINCT p.id) AS total_pagos,
                    SUM(p.monto) AS total_cobrado,
                    COUNT(DISTINCT r.id) AS rendiciones,
                    SUM(r.monto_declarado) AS total_rendido,
                    SUM(r.monto_recibido) AS total_recibido
             FROM usuarios u
             LEFT JOIN pagos p ON p.cobrador_id = u.id
                 AND DATE(p.created_at) >= ?
                 AND p.estado != 'anulado'
             LEFT JOIN rendiciones r ON r.cobrador_id = u.id
                 AND r.fecha >= ?
                 AND r.estado = 'confirmada'
             WHERE u.rol IN ('cobrador','vendedor') AND u.activo = 1
             GROUP BY u.id, u.nombre
             ORDER BY total_cobrado DESC"
        );
        $stats->execute([$desde, $desde]);
        $cobradores = $stats->fetchAll();

        // Histórico diario (últimos 30 días)
        $historico = $this->db->prepare(
            "SELECT DATE(p.created_at) AS fecha,
                    SUM(p.monto) AS total
             FROM pagos p
             WHERE p.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
               AND p.estado != 'anulado'
             GROUP BY DATE(p.created_at)
             ORDER BY fecha ASC"
        );
        $historico->execute();
        $historicoDias = $historico->fetchAll();

        $this->view('admin/reportes/cobradores', compact(
            'cobradores', 'historicoDias', 'periodo', 'desde'
        ));
    }

    // ——— HELPER: CSV export ————————————————————————————————
    private function exportCsvCartera(array $creditos): void
    {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="cartera_' . date('Ymd') . '.csv"');
        header('Pragma: no-cache');

        $out = fopen('php://output', 'w');
        // BOM para Excel
        fputs($out, "\xEF\xBB\xBF");

        fputcsv($out, [
            'ID', 'Cliente', 'DNI', 'Sucursal', 'Cobrador',
            'Prestado', 'A devolver', 'Cuotas', 'Frecuencia',
            'Mora acumulada', 'Mora pagada', 'Estado', 'Inicio'
        ], ';');

        foreach ($creditos as $c) {
            fputcsv($out, [
                $c['id'],
                $c['cliente_nombre'],
                $c['dni'],
                $c['sucursal_nombre'],
                $c['cobrador_nombre'] ?? '',
                number_format((float)$c['monto_prestado'], 2, ',', '.'),
                number_format((float)$c['monto_a_devolver'], 2, ',', '.'),
                $c['cantidad_cuotas'],
                $c['frecuencia'],
                number_format((float)$c['mora_acumulada'], 2, ',', '.'),
                number_format((float)$c['mora_pagada'], 2, ',', '.'),
                $c['estado'],
                $c['fecha_inicio'],
            ], ';');
        }

        fclose($out);
        exit;
    }

    // ——— QUERIES PRIVADAS —————————————————————————————————
    private function getCartera(array $f): array
    {
        $where  = ["cr.estado = 'activo'"];
        $params = [];

        if ($f['sucursal_id']) {
            $where[]  = 'cr.sucursal_id = ?';
            $params[] = $f['sucursal_id'];
        }
        if ($f['cobrador_id']) {
            $where[]  = 'cr.cobrador_id = ?';
            $params[] = $f['cobrador_id'];
        }
        if ($f['frecuencia']) {
            $where[]  = 'cr.frecuencia = ?';
            $params[] = $f['frecuencia'];
        }
        if ($f['desde']) {
            $where[]  = 'cr.fecha_inicio >= ?';
            $params[] = $f['desde'];
        }
        if ($f['hasta']) {
            $where[]  = 'cr.fecha_inicio <= ?';
            $params[] = $f['hasta'];
        }

        $sql = "SELECT cr.*, cl.nombre AS cliente_nombre, cl.dni,
                       s.nombre AS sucursal_nombre,
                       u_cob.nombre AS cobrador_nombre,
                       -- cuotas pagadas
                       (SELECT COUNT(*) FROM cuotas WHERE credito_id = cr.id AND estado = 'pagada') AS cuotas_pagadas,
                       -- saldo pendiente
                       (cr.monto_a_devolver -
                           COALESCE((SELECT SUM(monto_a_capital) FROM pagos p2
                                     JOIN cuotas cu2 ON p2.cuota_id = cu2.id
                                     WHERE cu2.credito_id = cr.id AND p2.estado != 'anulado'), 0)
                       ) AS saldo_capital
                FROM creditos cr
                JOIN clientes cl ON cr.cliente_id = cl.id
                JOIN sucursales s ON cr.sucursal_id = s.id
                LEFT JOIN usuarios u_cob ON cr.cobrador_id = u_cob.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY cr.fecha_inicio DESC
                LIMIT 500";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function resumenCartera(array $creditos): array
    {
        return [
            'total_creditos'  => count($creditos),
            'total_prestado'  => array_sum(array_column($creditos, 'monto_prestado')),
            'total_devolver'  => array_sum(array_column($creditos, 'monto_a_devolver')),
            'saldo_capital'   => array_sum(array_column($creditos, 'saldo_capital')),
            'mora_acumulada'  => array_sum(array_column($creditos, 'mora_acumulada')),
            'mora_pendiente'  => array_sum(array_column($creditos, 'mora_acumulada'))
                               - array_sum(array_column($creditos, 'mora_pagada')),
        ];
    }

    private function getSucursales(): array
    {
        return $this->db->query(
            "SELECT id, nombre FROM sucursales WHERE activa = 1 ORDER BY nombre"
        )->fetchAll();
    }

    private function getCobradores(): array
    {
        return $this->db->query(
            "SELECT id, nombre FROM usuarios WHERE rol IN ('cobrador','vendedor') AND activo = 1 ORDER BY nombre"
        )->fetchAll();
    }
}
