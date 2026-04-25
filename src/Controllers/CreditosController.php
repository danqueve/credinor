<?php
// src/Controllers/CreditosController.php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Credito;
use App\Models\Cliente;
use App\Models\Cuota;
use App\Models\Garante;
use App\Models\Usuario;
use App\Services\CreditoService;

class CreditosController extends Controller
{
    private Credito $model;
    private CreditoService $service;

    public function __construct()
    {
        $this->model   = new Credito();
        $this->service = new CreditoService();
    }

    // ——— VENDEDOR ———————————————————————————————————

    // GET /vendedor/creditos
    public function misCreditos(): void
    {
        $sucursalId = Auth::sucursalId();
        $estado     = Request::get('estado', '');
        $creditos   = $this->model->getBySucursal($sucursalId, $estado);
        $this->view('vendedor/creditos', compact('creditos', 'estado'));
    }

    // GET /vendedor/creditos/nuevo?cliente_id=X
    public function nuevo(): void
    {
        $clienteId = (int) Request::get('cliente_id', 0);
        $cliente   = $clienteId ? (new Cliente())->find($clienteId) : null;
        $clientes  = (new Cliente())->searchBySucursal(Auth::sucursalId(), '');
        $garantes  = (new Garante())->all('nombre');

        $this->view('vendedor/credito_form', compact('cliente', 'clientes', 'garantes'));
    }

    // POST /vendedor/creditos
    public function store(): void
    {
        $this->validateCsrf();

        try {
            $id = $this->service->crearSolicitud([
                'sucursal_id'            => Auth::sucursalId(),
                'cliente_id'             => (int) Request::post('cliente_id'),
                'garante_id'             => Request::post('garante_id'),
                'monto_prestado'         => Request::post('monto_prestado'),
                'monto_a_devolver'       => Request::post('monto_a_devolver'),
                'cantidad_cuotas'        => Request::post('cantidad_cuotas'),
                'frecuencia'             => Request::post('frecuencia'),
                'fecha_inicio'           => Request::post('fecha_inicio'),
                'fecha_primera_cuota'    => Request::post('fecha_primera_cuota'),
                'aplica_mora'            => Request::post('aplica_mora', 1),
                'porcentaje_mora_diaria' => Request::post('porcentaje_mora_diaria'),
                'observaciones'          => Request::post('observaciones'),
            ]);

            Session::flash('success', 'Solicitud de crédito creada. Quedará pendiente de autorización.');
            Response::redirect('/vendedor/creditos/' . $id);
        } catch (\DomainException $e) {
            Session::flash('error', $e->getMessage());
            Response::redirect('/vendedor/creditos/nuevo');
        }
    }

    // ——— COMPARTIDO ———————————————————————————————————

    // GET /creditos/{id}
    public function show(array $params): void
    {
        $credito  = $this->model->getConDetalles((int) $params['id']);
        if (!$credito) Response::abort(404, 'Crédito no encontrado.');

        $cuotas   = (new Cuota())->getByCreditoOrdenadas((int) $params['id']);
        $log      = $this->model->getLog((int) $params['id']);

        $layout = Auth::isAdmin() ? 'admin' : (Auth::isVendedor() ? 'vendedor' : 'cobrador');
        $this->view('shared/credito_detalle', compact('credito', 'cuotas', 'log'));
    }

    // ——— ADMIN ———————————————————————————————————————

    // GET /admin/creditos
    public function adminIndex(): void
    {
        $estado   = Request::get('estado', 'pendiente_autorizacion');
        $creditos = $this->model->getAdminListado($estado);
        $this->view('admin/creditos', compact('creditos', 'estado'));
    }

    // GET /admin/creditos/{id}/autorizar  (formulario)
    public function formAutorizar(array $params): void
    {
        $credito  = $this->model->getConDetalles((int) $params['id']);
        if (!$credito) Response::abort(404);
        $cuotas   = (new Cuota())->getByCreditoOrdenadas((int) $params['id']);
        $cobradores = (new Usuario())->getCobradores();
        $this->view('admin/autorizar_credito', compact('credito', 'cuotas', 'cobradores'));
    }

    // POST /admin/creditos/{id}/autorizar
    public function autorizar(array $params): void
    {
        $this->validateCsrf();
        $this->requireRole('admin');

        try {
            $cobradorId = (int) Request::post('cobrador_id');
            if (!$cobradorId) throw new \DomainException('Seleccioná un cobrador.');

            $this->service->autorizar((int) $params['id'], $cobradorId);

            Session::flash('success', 'Crédito autorizado y cuotas generadas correctamente.');
            Response::redirect('/admin/creditos?estado=activo');
        } catch (\DomainException $e) {
            Session::flash('error', $e->getMessage());
            Response::redirect('/admin/creditos/' . $params['id'] . '/autorizar');
        }
    }

    // POST /admin/creditos/{id}/rechazar
    public function rechazar(array $params): void
    {
        $this->validateCsrf();
        $this->requireRole('admin');

        $motivo = trim(Request::post('motivo', ''));
        if (!$motivo) {
            Session::flash('error', 'Indicá el motivo del rechazo.');
            Response::redirect('/admin/creditos/' . $params['id'] . '/autorizar');
        }

        try {
            $this->service->rechazar((int) $params['id'], $motivo);
            Session::flash('success', 'Crédito rechazado.');
            Response::redirect('/admin/creditos');
        } catch (\DomainException $e) {
            Session::flash('error', $e->getMessage());
            Response::redirect('/admin/creditos');
        }
    }
}
