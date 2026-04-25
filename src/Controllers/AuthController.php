<?php
// src/Controllers/AuthController.php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Usuario;

class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }
        $this->view('auth/login', [], 'auth');
    }

    public function login(): void
    {
        $this->validateCsrf();

        $username = trim(Request::post('username', ''));
        $password = Request::post('password', '');

        if (!$username || !$password) {
            Session::flash('error', 'Ingresá usuario y contraseña.');
            Response::redirect('/login');
        }

        $usuario = new Usuario();
        $user    = $usuario->findByUsername($username);

        if (!$user || !Auth::verifyPassword($password, $user['password'])) {
            Session::flash('error', 'Credenciales incorrectas.');
            Response::redirect('/login');
        }

        if ((int)$user['activo'] !== 1) {
            Session::flash('error', 'Tu cuenta está deshabilitada. Contactá al administrador.');
            Response::redirect('/login');
        }

        Auth::login($user);
        Response::redirect('/dashboard');
    }

    public function logout(): void
    {
        Auth::logout();
        Response::redirect('/login');
    }
}
