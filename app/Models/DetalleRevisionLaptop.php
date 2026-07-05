<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleRevisionLaptop extends Model
{
    protected $table = 'detalle_revision_laptop';

    protected $fillable = [
        'orden_reparacion_id',
        'teclado',
        'pantalla',
        'cargador',
        'disco_duro',
        'memoria_ram',
        'bisagras',
        'wifi',
        'encendido',
        'observaciones',
    ];

    public function orden()
    {
        return $this->belongsTo(OrdenReparacion::class, 'orden_reparacion_id');
    }
}
