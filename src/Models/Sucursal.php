<?php
// src/Models/Sucursal.php
namespace App\Models;

use App\Core\Model;

class Sucursal extends Model
{
    protected string $table = 'sucursales';
    protected array $fillable = [
        'nombre', 'direccion', 'telefono', 'activa',
    ];

    public function getActivas(): array
    {
        return $this->query(
            'SELECT * FROM sucursales WHERE activa = 1 ORDER BY nombre'
        )->fetchAll();
    }
}
