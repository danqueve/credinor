<?php
// src/Helpers/PhoneHelper.php
namespace App\Helpers;

class PhoneHelper
{
    /**
     * Retorna la URL de WhatsApp para el número dado (Argentina: prefijo 549).
     * Acepta formatos: 2994123456 / 0299-4123456 / +549 299 4123456
     */
    public static function waUrl(string $telefono, string $pais = '549'): string
    {
        $digits = preg_replace('/\D/', '', $telefono);
        // Quitar 0 inicial si viene con discado local
        $digits = ltrim($digits, '0');
        return 'https://wa.me/' . $pais . $digits;
    }

    /**
     * Formatea un teléfono para mostrar: (299) 412-3456
     */
    public static function format(string $telefono): string
    {
        $digits = preg_replace('/\D/', '', $telefono);
        if (strlen($digits) === 10) {
            return '(' . substr($digits, 0, 3) . ') ' .
                   substr($digits, 3, 3) . '-' .
                   substr($digits, 6);
        }
        return $telefono;
    }
}
