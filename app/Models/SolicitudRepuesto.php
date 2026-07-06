<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudRepuesto extends Model
{
    protected $table = 'solicitudes_repuestos';

    protected $fillable = [
        'sucursal_id',
        'solicitante_id',
        'almacenista_id',
        'orden_reparacion_id',
        'nombre_repuesto',
        'cantidad',
        'urgencia',
        'estado',
        'confirmado_recibido',
        'fecha_confirmacion',
        'leido_por_solicitante',
        'observaciones_solicitante',
        'observaciones_almacenista',
        'fecha_respuesta',
    ];

    protected $casts = [
        'fecha_respuesta' => 'datetime',
        'fecha_confirmacion' => 'datetime',
        'confirmado_recibido' => 'boolean',
        'leido_por_solicitante' => 'boolean',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    public function almacenista()
    {
        return $this->belongsTo(User::class, 'almacenista_id');
    }

    public function ordenReparacion()
    {
        return $this->belongsTo(OrdenReparacion::class, 'orden_reparacion_id');
    }

    public function estadoLabel()
    {
        return match ($this->estado) {
            'pendiente' => 'Pendiente',
            'enviado' => 'Enviado',
            'agotado' => 'Agotado',
            'no_existe' => 'No existe',
            default => $this->estado,
        };
    }

    public function urgenciaLabel()
    {
        return match ($this->urgencia) {
            'alta' => 'Alta',
            'media' => 'Media',
            'baja' => 'Baja',
            default => $this->urgencia,
        };
    }
}
