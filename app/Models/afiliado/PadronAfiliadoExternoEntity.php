<?php

namespace App\Models\Afiliado;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PadronAfiliadoExternoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_padron_afiliados_api_exterior';
    protected $primaryKey = 'id_padron';
    public $timestamps = false;

    protected $fillable = [
        'cuil_titular',
        'padron_cuil',
        'padron_afiliado',
        'dni',
        'apellido',
        'nombres',
        'sexo',
        'estado_civil',
        'fecha_baja',
        'id_parentesco',
        'observaciones',
        'fecha_nacimiento',
        'tipo_plan',
        'sindical',
        'activo',
        'macheo',
        'verificado'
    ];
}
