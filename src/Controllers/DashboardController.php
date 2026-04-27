<?php
// src/Controllers/DashboardController.php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Models\Credito;
use App\Models\Cuota;
use App\Models\Rendicion;
use App\Models\Usuario;

class DashboardController extends Controller
{
    public function index(): void
    {
        $rol = Auth::rol();

        match ($rol) {
            'admin'             => $this->adminDashboard(),
            'vendedor','cobrador' => $this->staffDashboard(),
            default             => Response::redirect('/login'),
        };
    }

    private function adminDashboard(): void
    {
        $db      = Database::getInstance();
        $credito = new Credito();

        // KPIs reales
        $cobradoHoy = (float) $db->query(
            "SELECT COALESCE(SUM(monto), 0) FROM pagos
             WHERE DATE(created_at) = CURDATE() AND estado != 'anulado'"
        )->fetchColumn();

        $pagosHoy = (int) $db->query(
            "SELECT COUNT(*) FROM pagos
             WHERE DATE(created_at) = CURDATE() AND estado != 'anulado'"
        )->fetchColumn();

        $cuotasVencerHoy = (int) $db->query(
            "SELECT COUNT(*) FROM cuotas
             WHERE fecha_vencimiento = CURDATE() AND estado IN ('pendiente','parcial')"
        )->fetchColumn();

        $carteraTotal = (float) $db->query(
            "SELECT COALESCE(SUM(monto_a_devolver -
                COALESCE((SELECT SUM(monto_a_capital) FROM pagos p
                           JOIN cuotas cu ON p.cuota_id = cu.id
                           WHERE cu.credito_id = cr.id AND p.estado != 'anulado'), 0)
            ), 0) FROM creditos cr WHERE estado = 'activo'"
        )->fetchColumn();

        $moraPendiente = (float) $db->query(
            "SELECT COALESCE(SUM(mora_acumulada - mora_pagada), 0)
             FROM creditos WHERE estado = 'activo'"
        )->fetchColumn();

        $kpis = [
            'creditos_pendientes'   => $credito->countPendientes(),
            'creditos_activos'      => $credito->countActivos(),
            'mora_pendiente'        => $moraPendiente,
            'rendiciones_pendientes'=> (int) $db->query(
                "SELECT COUNT(*) FROM rendiciones WHERE estado = 'pendiente'"
            )->fetchColumn(),
            'cobrado_hoy'           => $cobradoHoy,
            'pagos_hoy'             => $pagosHoy,
            'cuotas_vencer_hoy'     => $cuotasVencerHoy,
            'cartera_total'         => $carteraTotal,
        ];

        $rendicionesPendientes = (new Rendicion())->getPendientesAdmin();

        // Nombre de la sucursal del admin (si tiene asignada)
        $sucursalNombre = 'Global';

        $this->view('admin/dashboard', compact('kpis', 'rendicionesPendientes', 'sucursalNombre'));
    }

    private function staffDashboard(): void
    {
        $credito  = new Credito();
        $cuota    = new Cuota();
        $sucursal = Auth::sucursalId();
        $userId   = Auth::id();

        $cobradoHoy = $cuota->totalCobradoHoy($userId);
        $hoy        = $cuota->getAgendaHoy($userId);
        $vencidas   = $cuota->getAgendaVencida($userId);

        $data = [
            'mis_pendientes'  => $credito->countPendientesBySucursal($sucursal),
            'mis_activos'     => $credito->countActivosBySucursal($sucursal),
            'cobrado_hoy'     => $cobradoHoy,
            'cuotas_hoy'      => count($hoy),
            'cuotas_vencidas' => count($vencidas),
        ];
        $this->view('vendedor/dashboard', $data);
    }
}
