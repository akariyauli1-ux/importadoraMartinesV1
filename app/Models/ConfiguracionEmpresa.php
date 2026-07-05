<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionEmpresa extends Model
{
    protected $table = 'configuraciones_empresa';

    protected $fillable = [
        'nombre_comercial',
        'logo_path',
    ];
}
