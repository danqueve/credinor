<?php
// src/Core/Auth.php
namespace App\Core;

class Auth
{
    public static function login(array $user): void
    {
        session_regenerate_id(true);
        Session::set('user', [
            'id'          => $user['id'],
            'nombre'      => $user['nombre'],
            'username'    => $user['username'],
            'rol'         => $user['rol'],
            'sucursal_id' => $user['sucursal_id'],
        ]);
    }

    public static function logout(): void
    {
        Session::remove('user');
        Session::destroy();
    }

    public static function check(): bool
    {
        return Session::has('user');
    }

    public static function user(): ?array
    {
        return Session::get('user');
    }

    public static function id(): ?int
    {
        return Session::get('user')['id'] ?? null;
    }

    public static function rol(): ?string
    {
        return Session::get('user')['rol'] ?? null;
    }

    public static function sucursalId(): ?int
    {
        return Session::get('user')['sucursal_id'] ?? null;
    }

    public static function isAdmin(): bool
    {
        return self::rol() === 'admin';
    }

    public static function isVendedor(): bool
    {
        return self::rol() === 'vendedor';
    }

    public static function isCobrador(): bool
    {
        return self::rol() === 'cobrador';
    }

    public static function can(array $roles): bool
    {
        return in_array(self::rol(), $roles, true);
    }

    /** Hashear contraseña */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /** Verificar contraseña */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
