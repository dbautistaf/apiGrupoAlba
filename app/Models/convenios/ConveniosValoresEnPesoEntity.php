<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConveniosValoresEnPesoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_valores_peso';
    protected $primaryKey = 'cod_valores_peso';
    public $timestamps = false;

    protected $fillable = [
        'cod_prestacion',
        'monto_especialista',
        'monto_ayudante',
        'monto_anestesia',
        'monto_gastos',
        'fecha_inicio',
        'fecha_termino',
        'cod_usuario',
        'fecha_crea',
        'cod_convenio'
    ];
}
