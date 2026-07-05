<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoOrden extends Model
{
    protected $table = 'fotos_orden';

    protected $fillable = [
        'orden_reparacion_id',
        'foto_path',
        'tipo',
    ];

    public function orden()
    {
        return $this->belongsTo(OrdenReparacion::class, 'orden_reparacion_id');
    }
}
