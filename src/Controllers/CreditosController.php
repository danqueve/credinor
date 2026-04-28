<?php
// src/Controllers/CreditosController.php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\ValidationException;
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

        $payload = [
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
        ];

        try {
            $id = $this->service->crear($payload);
            Session::flash('success', 'Crédito creado y activado. Las cuotas fueron generadas correctamente.');
            Response::redirect('/creditos/' . $id);
        } catch (ValidationException $e) {
            $this->validationFailed($e, '/vendedor/creditos/nuevo');
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
        $frecuencia    = Request::get('frecuencia', '');
        $q             = trim(Request::get('q', ''));
        $soloPendientes = (bool) Request::get('solo_pendientes', false);
        $creditos      = $this->model->getAdminListado('', $q, $frecuencia);
        $stats         = $this->model->getAdminStats();
        $this->view('admin/creditos', compact('creditos', 'frecuencia', 'q', 'stats', 'soloPendientes'));
    }

    // POST /admin/creditos/{id}/cancelar
    public function cancelar(array $params): void
    {
        $this->validateCsrf();
        $this->requireRole('admin');

        $motivo = trim(Request::post('motivo', ''));
        if (!$motivo) {
            Session::flash('error', 'El motivo de cancelación es obligatorio.');
            Response::redirect('/creditos/' . $params['id']);
        }

        try {
            $this->service->cancelar((int) $params['id'], Auth::id(), $motivo);
            Session::flash('success', 'Crédito cancelado correctamente.');
            Response::redirect('/admin/creditos');
        } catch (\DomainException $e) {
            Session::flash('error', $e->getMessage());
            Response::redirect('/creditos/' . $params['id']);
        }
    }

    // GET /admin/creditos/{id}/editar
    public function edit(array $params): void
    {
        $this->requireRole('admin');
        $credito    = $this->model->getConDetalles((int) $params['id']);
        if (!$credito) Response::abort(404, 'Crédito no encontrado.');

        $cliente = [
            'id'     => $credito['cliente_id'],
            'nombre' => $credito['cliente_nombre'],
            'dni'    => $credito['dni'],
        ];
        $cobradores  = (new Usuario())->getCobradores();
        $modoEdicion = true;
        $this->view('vendedor/credito_form', compact('credito', 'cliente', 'cobradores', 'modoEdicion'));
    }

    // POST /admin/creditos/{id}/editar
    public function update(array $params): void
    {
        $this->validateCsrf();
        $this->requireRole('admin');

        try {
            $this->service->actualizar((int) $params['id'], [
                'cobrador_id'            => (int) Request::post('cobrador_id'),
                'frecuencia'             => Request::post('frecuencia'),
                'cantidad_cuotas'        => (int) Request::post('cantidad_cuotas'),
                'monto_a_devolver'       => Request::post('monto_a_devolver'),
                'fecha_primera_cuota'    => Request::post('fecha_primera_cuota'),
                'observaciones'          => Request::post('observaciones'),
            ], Auth::id());

            Session::flash('success', 'Crédito actualizado correctamente.');
            Response::redirect('/creditos/' . $params['id']);
        } catch (\DomainException $e) {
            Session::flash('error', $e->getMessage());
            Response::redirect('/admin/creditos/' . $params['id'] . '/editar');
        }
    }
}
