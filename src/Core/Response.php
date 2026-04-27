<?php
// src/Core/Response.php
namespace App\Core;

class Response
{
    public static function redirect(string $url): void
    {
        header('Location: ' . config('url') . $url);
        exit;
    }

    public static function redirectTo(string $absoluteUrl): void
    {
        header('Location: ' . $absoluteUrl);
        exit;
    }

    public static function json(mixed $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function abort(int $code, string $message = ''): void
    {
        http_response_code($code);
        echo $message;
        exit;
    }
}
