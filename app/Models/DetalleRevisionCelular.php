<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleRevisionCelular extends Model
{
    protected $table = 'detalle_revision_celular';

    protected $fillable = [
        'orden_reparacion_id',
        'tactil',
        'pantalla',
        'camaras',
        'conector_carga',
        'bateria',
        'botones',
        'senal',
        'altavoz',
        'observaciones',
    ];

    public function orden()
    {
        return $this->belongsTo(OrdenReparacion::class, 'orden_reparacion_id');
    }
}
