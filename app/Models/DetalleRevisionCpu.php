<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleRevisionCpu extends Model
{
    protected $table = 'detalle_revision_cpu';

    protected $fillable = [
        'orden_reparacion_id',
        'fuente_poder',
        'tarjeta_madre',
        'procesador',
        'memoria_ram',
        'disco_duro',
        'tarjeta_video',
        'puertos_usb',
        'refrigeracion',
        'observaciones',
    ];

    public function orden()
    {
        return $this->belongsTo(OrdenReparacion::class, 'orden_reparacion_id');
    }
}
