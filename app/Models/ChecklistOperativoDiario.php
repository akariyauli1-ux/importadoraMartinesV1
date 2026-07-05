<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistOperativoDiario extends Model
{
    protected $table = 'checklist_operativo_diario';

    protected $fillable = [
        'sucursal_id',
        'fecha',
        'uniformes_completos',
        'limpieza_puestos',
        'herramientas_ordenadas',
        'observaciones',
        'registrado_por',
    ];

    protected $casts = [
        'uniformes_completos' => 'boolean',
        'limpieza_puestos' => 'boolean',
        'herramientas_ordenadas' => 'boolean',
        'fecha' => 'date',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
