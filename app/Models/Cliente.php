<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'nombre',
        'carnet_identidad',
        'telefono',
        'email',
        'direccion',
    ];

    public function ordenes()
    {
        return $this->hasMany(OrdenReparacion::class, 'cliente_id');
    }
}
