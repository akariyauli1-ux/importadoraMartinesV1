<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursales';

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    public function empleados()
    {
        return $this->hasMany(User::class, 'sucursal_id');
    }

    public function ordenes()
    {
        return $this->hasMany(OrdenReparacion::class, 'sucursal_id');
    }

    public function checklists()
    {
        return $this->hasMany(ChecklistOperativoDiario::class, 'sucursal_id');
    }
}
