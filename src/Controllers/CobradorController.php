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
        $this->requireStaff();
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
        $this->requireStaff();
        $pagos = (new Pago())->getDelCobrador(Auth::id(), 60);
        $this->view('cobrador/historial', compact('pagos'));
    }

    // GET /cobrador/caja
    public function caja(): void
    {
        $this->requireStaff();
        $cobradorId = Auth::id();
        $pagos      = (new Pago())->getDelDia($cobradorId);
        $total      = round(array_sum(array_column($pagos, 'monto')), 2);

        // Ver si ya cerró caja hoy
        $rendicion = (new Rendicion())->getDeHoy($cobradorId);

        $this->view('cobrador/cierre_caja', compact('pagos', 'total', 'rendicion'));
    }

    // POST /cobrador/caja/cerrar
    public function cerrarCaja(): void
    {
        $this->validateCsrf();
        $this->requireStaff();

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

    public function rendiciones(): void
    {
        $this->requireStaff();
        $rendiciones = (new Rendicion())->getDelCobrador(Auth::id());
        $this->view('cobrador/rendiciones', compact('rendiciones'));
    }

    public function rendicionDetalle(array $params): void
    {
        $this->requireStaff();
        $rendicion = (new Rendicion())->getDelCobradorConPagos((int)$params['id'], Auth::id());
        if (!$rendicion) Response::abort(404, 'Rendición no encontrada.');
        $this->view('cobrador/rendicion_detalle', compact('rendicion'));
    }
}
