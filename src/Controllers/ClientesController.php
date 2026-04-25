<?php
// src/Controllers/ClientesController.php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Cliente;

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
        $this->view('vendedor/cliente_form', ['cliente' => null, 'accion' => 'crear']);
    }

    // POST /vendedor/clientes
    public function store(): void
    {
        $this->validateCsrf();

        $dni    = trim(Request::post('dni', ''));
        $nombre = trim(Request::post('nombre', ''));

        if (!$dni || !$nombre) {
            Session::flash('error', 'DNI y nombre son obligatorios.');
            Response::redirect('/vendedor/clientes/nuevo');
        }

        // Verificar DNI único
        if ($this->model->findByDni($dni)) {
            Session::flash('error', "Ya existe un cliente con DNI {$dni}.");
            Response::redirect('/vendedor/clientes/nuevo');
        }

        $id = $this->model->create([
            'sucursal_id'  => Auth::sucursalId(),
            'vendedor_id'  => Auth::id(),
            'dni'          => $dni,
            'nombre'       => $nombre,
            'telefono'     => trim(Request::post('telefono', '')),
            'email'        => trim(Request::post('email', '')),
            'domicilio'    => trim(Request::post('domicilio', '')),
            'localidad'    => trim(Request::post('localidad', '')),
            'lat'          => Request::post('lat') ?: null,
            'lng'          => Request::post('lng') ?: null,
            'observaciones'=> trim(Request::post('observaciones', '')),
        ]);

        Session::flash('success', "Cliente {$nombre} creado correctamente.");
        Response::redirect('/vendedor/clientes/' . $id);
    }

    // GET /vendedor/clientes/{id}
    public function show(array $params): void
    {
        $cliente = $this->model->findOrFail((int) $params['id']);
        // Créditos del cliente
        $creditos = (new \App\Models\Credito())->getByCliente((int) $params['id']);
        $layout = Auth::isAdmin() ? 'admin' : 'vendedor';
        $this->view('shared/cliente_detalle', compact('cliente', 'creditos'));
    }

    // GET /vendedor/clientes/{id}/editar
    public function editar(array $params): void
    {
        $cliente = $this->model->findOrFail((int) $params['id']);
        $this->view('vendedor/cliente_form', ['cliente' => $cliente, 'accion' => 'editar']);
    }

    // POST /vendedor/clientes/{id}/editar
    public function update(array $params): void
    {
        $this->validateCsrf();

        $id     = (int) $params['id'];
        $cliente= $this->model->findOrFail($id);

        $this->model->update($id, [
            'dni'           => trim(Request::post('dni', $cliente['dni'])),
            'nombre'        => trim(Request::post('nombre', $cliente['nombre'])),
            'telefono'      => trim(Request::post('telefono', '')),
            'email'         => trim(Request::post('email', '')),
            'domicilio'     => trim(Request::post('domicilio', '')),
            'localidad'     => trim(Request::post('localidad', '')),
            'lat'           => Request::post('lat') ?: null,
            'lng'           => Request::post('lng') ?: null,
            'observaciones' => trim(Request::post('observaciones', '')),
        ]);

        Session::flash('success', 'Cliente actualizado correctamente.');
        Response::redirect('/vendedor/clientes/' . $id);
    }

    // GET /api/clientes/buscar?q=... (JSON para autocompletar en formulario de crédito)
    public function buscarJson(): void
    {
        $q = trim(Request::get('q', ''));
        $sucursalId = Auth::sucursalId() ?? 0;

        if (strlen($q) < 2) {
            $this->json([]);
        }

        $clientes = $this->model->searchBySucursal($sucursalId, $q);
        $this->json(array_map(fn($c) => [
            'id'     => $c['id'],
            'label'  => $c['nombre'] . ' — DNI ' . $c['dni'],
            'nombre' => $c['nombre'],
            'dni'    => $c['dni'],
        ], $clientes));
    }
}
