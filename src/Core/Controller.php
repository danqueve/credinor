<?php
// src/Core/Controller.php
namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = [], string $layout = 'app'): void
    {
        View::make($view, $data, $layout);
    }

    protected function redirect(string $url): void
    {
        Response::redirect($url);
    }

    protected function json(mixed $data, int $code = 200): void
    {
        Response::json($data, $code);
    }

    protected function flash(string $type, string $message): void
    {
        Session::flash($type, $message);
    }

    protected function validateCsrf(): void
    {
        Request::verifyCsrf();
    }

    protected function requireRole(array|string $roles): void
    {
        $roles = (array) $roles;
        if (!Auth::can($roles)) {
            Response::redirect('/dashboard');
        }
    }

    protected function requireStaff(): void
    {
        $this->requireRole(Auth::STAFF);
    }

    /**
     * Store validation errors + old input in session, then redirect back.
     */
    protected function validationFailed(\App\Core\ValidationException $e, string $backUrl): never
    {
        $_SESSION['_errors'] = $e->getErrors();
        $_SESSION['_old']    = $_POST;
        Session::flash('error', 'Corrige los errores marcados en el formulario.');
        Response::redirect($backUrl);
    }
}
