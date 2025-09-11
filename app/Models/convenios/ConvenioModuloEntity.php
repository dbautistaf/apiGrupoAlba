<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConvenioModuloEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_modulos';
    protected $primaryKey = 'id_modulo';
    public $timestamps = false;

    protected $fillable = [
        'nombre_modulo',
        'descripcion_modulo',
        'fecha_crea',
        'id_convenio',
        'tipo_modulo'
    ];
}
