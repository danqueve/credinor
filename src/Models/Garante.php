<?php
// src/Models/Garante.php
namespace App\Models;

use App\Core\Model;

class Garante extends Model
{
    protected string $table = 'garantes';

    public function findByDni(string $dni): ?array
    {
        $row = $this->query(
            'SELECT * FROM garantes WHERE dni = ? LIMIT 1',
            [$dni]
        )->fetch();
        return $row ?: null;
    }
}
