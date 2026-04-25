<?php
// src/Controllers/CobradorController.php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Cuota;
use App\Models\Pago;
use App\Models\Rendicion;
use App\Services\RendicionService;

class CobradorController extends Controller
{
    // GET /cobrador/agenda
    public function agenda(): void
    {
        $this->requireRole('cobrador');
        $cobradorId = Auth::id();
        $cuota      = new Cuota();

        $hoy     = $cuota->getAgendaHoy($cobradorId);
        $vencida = $cuota->getAgendaVencida($cobradorId);
        $futura  = $cuota->getAgendaFutura($cobradorId, 7); // próximos 7 días

        $totalCobrado  = $cuota->totalCobradoHoy($cobradorId);
        $totalClientes = count($hoy) + count($vencida);

        $this->view('cobrador/agenda', compact(
            'hoy', 'vencida', 'futura', 'totalCobrado', 'totalClientes'
        ));
    }

    // GET /cobrador/historial
    public function historial(): void
    {
        $this->requireRole('cobrador');
        $pagos = (new Pago())->getDelCobrador(Auth::id(), 60);
        $this->view('cobrador/historial', compact('pagos'));
    }

    // GET /cobrador/caja
    public function caja(): void
    {
        $this->requireRole('cobrador');
        $cobradorId = Auth::id();
        $pagos      = (new Pago())->getDelDia($cobradorId);
        $total      = array_sum(array_column($pagos, 'monto'));

        // Ver si ya cerró caja hoy
        $rendicion = (new Rendicion())->getDeHoy($cobradorId);

        $this->view('cobrador/cierre_caja', compact('pagos', 'total', 'rendicion'));
    }

    // POST /cobrador/caja/cerrar
    public function cerrarCaja(): void
    {
        $this->validateCsrf();
        $this->requireRole('cobrador');

        try {
            $service = new RendicionService();
            $rendicionId = $service->cerrar(Auth::id(), Auth::sucursalId());
            Session::flash('success', 'Caja cerrada. Rendición enviada al administrador.');
            Response::redirect('/cobrador/caja');
        } catch (\DomainException $e) {
            Session::flash('error', $e->getMessage());
            Response::redirect('/cobrador/caja');
        }
    }
}
