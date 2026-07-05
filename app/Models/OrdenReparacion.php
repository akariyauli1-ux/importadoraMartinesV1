<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenReparacion extends Model
{
    protected $table = 'ordenes_reparacion';

    protected $fillable = [
        'numero_ticket',
        'cliente_id',
        'sucursal_id',
        'categoria',
        'marca',
        'modelo',
        'serie',
        'problema_reportado',
        'costo_estimado',
        'tecnico_id',
        'recepcionista_id',
        'estado',
        'firma_checkin_base64',
        'firma_checkout_base64',
        'firma_aprobacion_presupuesto_base64',
        'motivo_derivacion',
        'es_asignacion_cruzada',
    ];

    protected $casts = [
        'costo_estimado' => 'decimal:2',
        'es_asignacion_cruzada' => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    public function recepcionista()
    {
        return $this->belongsTo(User::class, 'recepcionista_id');
    }

    public function fotos()
    {
        return $this->hasMany(FotoOrden::class, 'orden_reparacion_id');
    }

    public function detalleRevisionCelular()
    {
        return $this->hasOne(DetalleRevisionCelular::class, 'orden_reparacion_id');
    }

    public function detalleRevisionLaptop()
    {
        return $this->hasOne(DetalleRevisionLaptop::class, 'orden_reparacion_id');
    }

    public function detalleRevisionElectrico()
    {
        return $this->hasOne(DetalleRevisionElectrico::class, 'orden_reparacion_id');
    }

    public function detalleRevisionCpu()
    {
        return $this->hasOne(DetalleRevisionCpu::class, 'orden_reparacion_id');
    }

    public function getRevision()
    {
        switch ($this->categoria) {
            case 'celular':
                return $this->detalleRevisionCelular;
            case 'laptop':
                return $this->detalleRevisionLaptop;
            case 'electrico':
                return $this->detalleRevisionElectrico;
            case 'cpu':
                return $this->detalleRevisionCpu;
            default:
                return null;
        }
    }
}
