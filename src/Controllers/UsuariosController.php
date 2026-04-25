<?php
// src/Controllers/UsuariosController.php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Usuario;
use App\Models\Sucursal;

class UsuariosController extends Controller
{
    private Usuario $model;

    public function __construct()
    {
        $this->model = new Usuario();
    }

    // GET /admin/usuarios
    public function index(): void
    {
        $this->requireRole('admin');
        $usuarios = $this->model->getConSucursal();
        $this->view('admin/usuarios', compact('usuarios'));
    }

    // GET /admin/usuarios/nuevo
    public function crear(): void
    {
        $this->requireRole('admin');
        $sucursales = (new Sucursal())->getActivas();
        $this->view('admin/usuario_form', ['usuario' => null, 'sucursales' => $sucursales, 'accion' => 'crear']);
    }

    // POST /admin/usuarios
    public function store(): void
    {
        $this->validateCsrf();
        $this->requireRole('admin');

        $username = trim(Request::post('username', ''));
        if (!$username) {
            Session::flash('error', 'El nombre de usuario es obligatorio.');
            Response::redirect('/admin/usuarios/nuevo');
        }
        if ($this->model->findByUsername($username)) {
            Session::flash('error', "El usuario «{$username}» ya está en uso.");
            Response::redirect('/admin/usuarios/nuevo');
        }

        $password = trim(Request::post('password', ''));
        if (strlen($password) < 6) {
            Session::flash('error', 'La contraseña debe tener al menos 6 caracteres.');
            Response::redirect('/admin/usuarios/nuevo');
        }

        $this->model->create([
            'sucursal_id' => (int) Request::post('sucursal_id'),
            'username'    => $username,
            'nombre'      => trim(Request::post('nombre', '')),
            'password'    => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
            'rol'         => Request::post('rol', 'vendedor'),
            'activo'      => 1,
        ]);

        Session::flash('success', 'Usuario creado correctamente.');
        Response::redirect('/admin/usuarios');
    }

    // GET /admin/usuarios/{id}/editar
    public function editar(array $params): void
    {
        $this->requireRole('admin');
        $usuario    = $this->model->findOrFail((int) $params['id']);
        $sucursales = (new Sucursal())->getActivas();
        $this->view('admin/usuario_form', compact('usuario', 'sucursales') + ['accion' => 'editar']);
    }

    // POST /admin/usuarios/{id}/editar
    public function update(array $params): void
    {
        $this->validateCsrf();
        $this->requireRole('admin');

        $id       = (int) $params['id'];
        $username = trim(Request::post('username', ''));

        if (!$username) {
            Session::flash('error', 'El nombre de usuario es obligatorio.');
            Response::redirect('/admin/usuarios/' . $id . '/editar');
        }

        // Verificar que el username no lo use otro usuario
        $existente = $this->model->findByUsername($username);
        if ($existente && (int)$existente['id'] !== $id) {
            Session::flash('error', "El usuario «{$username}» ya está en uso.");
            Response::redirect('/admin/usuarios/' . $id . '/editar');
        }

        $data = [
            'sucursal_id' => (int) Request::post('sucursal_id'),
            'username'    => $username,
            'nombre'      => trim(Request::post('nombre', '')),
            'rol'         => Request::post('rol', 'vendedor'),
            'activo'      => (int) Request::post('activo', 1),
        ];

        // Solo actualizar password si se ingresó una nueva
        $pw = trim(Request::post('password', ''));
        if ($pw) {
            if (\strlen($pw) < 6) {
                Session::flash('error', 'La contraseña debe tener al menos 6 caracteres.');
                Response::redirect('/admin/usuarios/' . $id . '/editar');
            }
            $data['password'] = password_hash($pw, PASSWORD_BCRYPT, ['cost' => 12]);
        }

        $this->model->update($id, $data);
        Session::flash('success', 'Usuario actualizado.');
        Response::redirect('/admin/usuarios');
    }

    // POST /admin/usuarios/{id}/toggle
    public function toggleActivo(array $params): void
    {
        $this->validateCsrf();
        $this->requireRole('admin');

        $id      = (int) $params['id'];
        $usuario = $this->model->findOrFail($id);

        // No desactivar el admin propio
        if ($id === Auth::id()) {
            Session::flash('error', 'No podés desactivar tu propia cuenta.');
            Response::redirect('/admin/usuarios');
        }

        $nuevo = $usuario['activo'] ? 0 : 1;
        $this->model->update($id, ['activo' => $nuevo]);

        Session::flash('success', 'Usuario ' . ($nuevo ? 'activado' : 'desactivado') . '.');
        Response::redirect('/admin/usuarios');
    }
}
