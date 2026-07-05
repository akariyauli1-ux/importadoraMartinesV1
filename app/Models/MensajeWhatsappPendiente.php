<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MensajeWhatsappPendiente extends Model
{
    protected $table = 'mensajes_whatsapp_pendientes';

    protected $fillable = [
        'orden_reparacion_id',
        'telefono',
        'mensaje',
        'enviado',
        'enviado_at',
    ];

    protected $casts = [
        'enviado' => 'boolean',
        'enviado_at' => 'datetime',
    ];

    public function orden()
    {
        return $this->belongsTo(OrdenReparacion::class, 'orden_reparacion_id');
    }
}
