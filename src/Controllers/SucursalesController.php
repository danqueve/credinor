<?php
// src/Controllers/SucursalesController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Sucursal;

class SucursalesController extends Controller
{
    private Sucursal $model;

    public function __construct()
    {
        $this->model = new Sucursal();
    }

    // GET /admin/sucursales
    public function index(): void
    {
        $this->requireRole('admin');
        $sucursales = $this->model->all('nombre');
        $this->view('admin/sucursales', compact('sucursales'));
    }

    // GET /admin/sucursales/nueva
    public function crear(): void
    {
        $this->requireRole('admin');
        $this->view('admin/sucursal_form', ['sucursal' => null, 'accion' => 'crear']);
    }

    // POST /admin/sucursales
    public function store(): void
    {
        $this->validateCsrf();
        $this->requireRole('admin');

        $nombre = trim(Request::post('nombre', ''));
        if (!$nombre) {
            Session::flash('error', 'El nombre de la sucursal es obligatorio.');
            Response::redirect('/admin/sucursales/nueva');
        }

        $this->model->create([
            'nombre'    => $nombre,
            'direccion' => trim(Request::post('direccion', '')),
            'telefono'  => trim(Request::post('telefono', '')),
            'activa'    => 1,
        ]);

        Session::flash('success', 'Sucursal creada correctamente.');
        Response::redirect('/admin/sucursales');
    }

    // GET /admin/sucursales/{id}/editar
    public function editar(array $params): void
    {
        $this->requireRole('admin');
        $sucursal = $this->model->findOrFail((int) $params['id']);
        $this->view('admin/sucursal_form', compact('sucursal') + ['accion' => 'editar']);
    }

    // POST /admin/sucursales/{id}/editar
    public function update(array $params): void
    {
        $this->validateCsrf();
        $this->requireRole('admin');

        $nombre = trim(Request::post('nombre', ''));
        if (!$nombre) {
            Session::flash('error', 'El nombre es obligatorio.');
            Response::redirect('/admin/sucursales/' . $params['id'] . '/editar');
        }

        $this->model->update((int) $params['id'], [
            'nombre'    => $nombre,
            'direccion' => trim(Request::post('direccion', '')),
            'telefono'  => trim(Request::post('telefono', '')),
            'activa'    => (int) Request::post('activa', 1),
        ]);

        Session::flash('success', 'Sucursal actualizada.');
        Response::redirect('/admin/sucursales');
    }

    // POST /admin/sucursales/{id}/toggle
    public function toggleActiva(array $params): void
    {
        $this->validateCsrf();
        $this->requireRole('admin');

        $id       = (int) $params['id'];
        $sucursal = $this->model->findOrFail($id);
        $nuevo    = $sucursal['activa'] ? 0 : 1;

        $this->model->update($id, ['activa' => $nuevo]);
        Session::flash('success', 'Sucursal ' . ($nuevo ? 'activada' : 'desactivada') . '.');
        Response::redirect('/admin/sucursales');
    }
}
