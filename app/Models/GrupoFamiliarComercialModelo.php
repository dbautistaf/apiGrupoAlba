<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoFamiliarComercialModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_familiar_comercial';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'cuil_titular',
        'cuil_benef',
        'dni',
        'apellidos',
        'nombres',
        'fec_nac',
        'nacionalidad',
        'sexo',
        'discapacidad',
        'id_parentesco',
        'id_estado_civil',
        'id_usuario'
    ];
}
