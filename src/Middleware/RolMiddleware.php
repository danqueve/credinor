<?php
// src/Middleware/RolMiddleware.php
namespace App\Middleware;

use App\Core\Auth;
use App\Core\Response;

class RolMiddleware
{
    public function __construct(private array $roles) {}

    public function handle(): void
    {
        if (!Auth::can($this->roles)) {
            http_response_code(403);
            require ROOT_PATH . '/views/errors/403.php';
            exit;
        }
    }
}
