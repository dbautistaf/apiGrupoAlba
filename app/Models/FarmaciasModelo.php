<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmaciasModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_farmacias';
    protected $primaryKey = 'id_farmacia';
    public $timestamps = false;

    protected $fillable = [
        'fecha_alta',
        'fecha_baja',
        'activo',
        'id_usuario',
        'cuit',
        'razon_social',
        'domicilio',
        'representante',
        'id_localidad',
        'id_partido',
        'id_provincia',
        'observaciones',
        'nombre_fantasia'
    ];
}
