<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SindicatosModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_sindicatos';
    protected $primaryKey = 'cod_sindicato';
    public $timestamps = false;

    protected $fillable = [
        'apellidos_responsable',
        'avatar_sindicato',
        'correo_responsable',
        'domicilio_sindicato',
        'estado',
        'nombres_responsable',
        'nombre_sindicato',
        'telefono_responsable',
        'telefono_sindicato'
    ];
}
