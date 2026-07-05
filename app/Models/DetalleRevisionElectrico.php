<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleRevisionElectrico extends Model
{
    protected $table = 'detalle_revision_electrico';

    protected $fillable = [
        'orden_reparacion_id',
        'fuente_alimentacion',
        'placa_principal',
        'salidas_audio',
        'entradas_video',
        'control_remoto',
        'botones_fisicos',
        'observaciones',
    ];

    public function orden()
    {
        return $this->belongsTo(OrdenReparacion::class, 'orden_reparacion_id');
    }
}
