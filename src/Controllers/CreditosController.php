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
use App\Models\Sucursal;
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
        $this->requireStaff();
        $sucursalId = Auth::sucursalId();
        $frecuencia = Request::get('frecuencia', '');
        $creditos   = $this->model->getBySucursal($sucursalId, $frecuencia);
        $this->view('vendedor/creditos', compact('creditos', 'frecuencia'));
    }

    // GET /vendedor/creditos/nuevo?cliente_id=X
    public function nuevo(): void
    {
        $this->requireRole(['admin', 'cobrador', 'vendedor']);

        $clienteId  = (int) Request::get('cliente_id', 0);
        $cliente    = $clienteId ? (new Cliente())->find($clienteId) : null;
        $sucursalId = Auth::sucursalId();

        // Admin ve todos los clientes; staff solo los de su sucursal
        $clientes = $sucursalId
            ? (new Cliente())->searchBySucursal($sucursalId, '')
            : (new Cliente())->searchAdmin('');

        $garantes   = (new Garante())->all('nombre');
        $sucursales = Auth::isAdmin() ? (new Sucursal())->getActivas() : [];

        $this->view('vendedor/credito_form', compact('cliente', 'clientes', 'garantes', 'sucursales'));
    }

    // POST /vendedor/creditos
    public function store(): void
    {
        $this->validateCsrf();
        $this->requireRole(['admin', 'cobrador', 'vendedor']);

        // Admin elige sucursal desde el form; staff usa la propia
        $sucursalId = Auth::isAdmin()
            ? (int) Request::post('sucursal_id', 0)
            : Auth::sucursalId();

        if (!$sucursalId) {
            Session::flash('error', 'Debes seleccionar una sucursal para el crédito.');
            Response::redirect('/vendedor/creditos/nuevo');
        }

        try {
            $id = $this->service->crearSolicitud([
                'sucursal_id'            => $sucursalId,
                'cliente_id'             => (int) Request::post('cliente_id'),
                'garante_id'             => Request::post('garante_id'),
                'monto_prestado'         => Request::post('monto_prestado'),
                'monto_a_devolver'       => Request::post('monto_a_devolver'),
                'cantidad_cuotas'        => Request::post('cantidad_cuotas'),
                'frecuencia'             => Request::post('frecuencia'),
                'fecha_inicio'           => Request::post('fecha_inicio'),
                'fecha_primera_cuota'    => Request::post('fecha_primera_cuota'),
                'aplica_mora'            => Request::post('aplica_mora', 0),
                'porcentaje_mora_diaria' => Request::post('porcentaje_mora_diaria'),
                'observaciones'          => Request::post('observaciones'),
            ]);

            Session::flash('success', 'Crédito creado y activado. Las cuotas fueron generadas correctamente.');
            Response::redirect('/creditos/' . $id);
        } catch (\DomainException $e) {
            Session::flash('error', $e->getMessage());
            Response::redirect('/vendedor/creditos/nuevo');
        }
    }

    // ——— COMPARTIDO ———————————————————————————————————

    // GET /creditos/{id}
    public function show(array $params): void
    {
        $this->requireRole(['admin', 'cobrador', 'vendedor']);
        $credito  = $this->model->getConDetalles((int) $params['id']);
        if (!$credito) Response::abort(404, 'Crédito no encontrado.');

        $cuotas   = (new Cuota())->getByCreditoOrdenadas((int) $params['id']);
        $log      = $this->model->getLog((int) $params['id']);

        $this->view('shared/credito_detalle', compact('credito', 'cuotas', 'log'));
    }

    // ——— ADMIN ———————————————————————————————————————

    // GET /admin/creditos
    public function adminIndex(): void
    {
        $frecuencia  = Request::get('frecuencia', '');
        $q           = trim(Request::get('q', ''));
        // ?pendientes=1 muestra sólo los créditos esperando autorización
        $soloEstado  = Request::get('pendientes') ? 'pendiente_autorizacion' : '';
        $creditos    = $this->model->getAdminListado($soloEstado, $q, $frecuencia);
        $stats       = $this->model->getAdminStats();
        $soloPendientes = (bool) Request::get('pendientes');
        $this->view('admin/creditos', compact('creditos', 'frecuencia', 'q', 'stats', 'soloPendientes'));
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
