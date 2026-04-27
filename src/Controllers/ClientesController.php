<?php
// src/Controllers/ClientesController.php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Cliente;
use App\Models\Sucursal;

class ClientesController extends Controller
{
    private Cliente $model;

    public function __construct()
    {
        $this->model = new Cliente();
    }

    // GET /vendedor/clientes  |  GET /admin/clientes
    public function index(): void
    {
        $this->requireRole(['admin', 'cobrador', 'vendedor']);
        $sucursalId = Auth::isAdmin() ? null : Auth::sucursalId();
        $q = trim(Request::get('q', ''));

        if ($sucursalId) {
            $clientes = $this->model->searchBySucursal($sucursalId, $q);
        } else {
            $clientes = $this->model->searchAdmin($q);
        }

        $layout = Auth::isAdmin() ? 'admin' : 'vendedor';
        $this->view("{$layout}/clientes", compact('clientes', 'q'));
    }

    // GET /vendedor/clientes/nuevo
    public function crear(): void
    {
        $this->requireRole(['admin', 'cobrador', 'vendedor']);
        $sucursales = Auth::isAdmin() ? (new Sucursal())->getActivas() : [];
        $this->view('vendedor/cliente_form', [
            'cliente'    => null,
            'accion'     => 'crear',
            'sucursales' => $sucursales,
        ]);
    }

    // POST /vendedor/clientes
    public function store(): void
    {
        $this->validateCsrf();
        $this->requireRole(['admin', 'cobrador', 'vendedor']);

        $dni    = trim(Request::post('dni', ''));
        $nombre = trim(Request::post('nombre', ''));

        if (!$dni || !$nombre) {
            Session::flash('error', 'DNI y nombre son obligatorios.');
            Response::redirect('/vendedor/clientes/nuevo');
        }

        // El admin elige sucursal desde el form; staff usa la propia
        $sucursalId = Auth::isAdmin()
            ? (int) Request::post('sucursal_id', 0)
            : Auth::sucursalId();

        if (!$sucursalId) {
            Session::flash('error', 'Debes seleccionar una sucursal.');
            Response::redirect('/vendedor/clientes/nuevo');
        }

        // Verificar DNI único
        if ($this->model->findByDni($dni)) {
            Session::flash('error', "Ya existe un cliente con DNI {$dni}.");
            Response::redirect('/vendedor/clientes/nuevo');
        }

        $id = $this->model->create([
            'sucursal_id'  => $sucursalId,
            'vendedor_id'  => Auth::id(),
            'dni'          => $dni,
            'nombre'       => $nombre,
            'telefono'     => trim(Request::post('telefono', '')),
            'domicilio'    => trim(Request::post('domicilio', '')),
            'localidad'    => trim(Request::post('localidad', '')),
            'observaciones'=> trim(Request::post('observaciones', '')),
        ]);

        Session::flash('success', "Cliente {$nombre} creado correctamente.");
        Response::redirect('/vendedor/clientes/' . $id);
    }

    // GET /vendedor/clientes/{id}
    public function show(array $params): void
    {
        $this->requireRole(['admin', 'cobrador', 'vendedor']);
        $cliente  = $this->model->findOrFail((int) $params['id']);
        $creditos = (new \App\Models\Credito())->getByCliente((int) $params['id']);
        $this->view('shared/cliente_detalle', compact('cliente', 'creditos'));
    }

    // GET /vendedor/clientes/{id}/editar
    public function editar(array $params): void
    {
        $this->requireRole(['admin', 'cobrador', 'vendedor']);
        $cliente    = $this->model->findOrFail((int) $params['id']);
        $sucursales = Auth::isAdmin() ? (new Sucursal())->getActivas() : [];
        $this->view('vendedor/cliente_form', [
            'cliente'    => $cliente,
            'accion'     => 'editar',
            'sucursales' => $sucursales,
        ]);
    }

    // POST /vendedor/clientes/{id}/editar
    public function update(array $params): void
    {
        $this->validateCsrf();
        $this->requireRole(['admin', 'cobrador', 'vendedor']);

        $id      = (int) $params['id'];
        $cliente = $this->model->findOrFail($id);

        $data = [
            'dni'           => trim(Request::post('dni', $cliente['dni'])),
            'nombre'        => trim(Request::post('nombre', $cliente['nombre'])),
            'telefono'      => trim(Request::post('telefono', '')),
            'domicilio'     => trim(Request::post('domicilio', '')),
            'localidad'     => trim(Request::post('localidad', '')),
            'observaciones' => trim(Request::post('observaciones', '')),
        ];

        // Admin puede reasignar sucursal
        if (Auth::isAdmin() && Request::post('sucursal_id')) {
            $data['sucursal_id'] = (int) Request::post('sucursal_id');
        }

        $this->model->update($id, $data);

        Session::flash('success', 'Cliente actualizado correctamente.');
        Response::redirect('/vendedor/clientes/' . $id);
    }

    // GET /api/clientes/buscar?q=... (JSON para autocompletar en formulario de crédito)
    public function buscarJson(): void
    {
        $this->requireRole(['admin', 'cobrador', 'vendedor']);
        $q = trim(Request::get('q', ''));

        $sucursalId = Auth::isAdmin() ? null : Auth::sucursalId();

        if (strlen($q) < 2) {
            $this->json([]);
        }

        $clientes = $sucursalId
            ? $this->model->searchBySucursal($sucursalId, $q)
            : $this->model->searchAdmin($q);

        $this->json(array_map(fn($c) => [
            'id'     => $c['id'],
            'label'  => $c['nombre'] . ' — DNI ' . $c['dni'],
            'nombre' => $c['nombre'],
            'dni'    => $c['dni'],
        ], $clientes));
    }
}
