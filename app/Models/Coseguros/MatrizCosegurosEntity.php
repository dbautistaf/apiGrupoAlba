<?php

namespace App\Models\Coseguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatrizCosegurosEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_matriz_coseguros';
    protected $primaryKey = 'id_coseguro';
    public $timestamps = false;

    protected $fillable = [
        'descripcion_coseguro',
        'fecha_carga',
        'periodo_desde',
        'periodo_hasta',
        'monto_regimen_general',
        'monto_monotr_autonomo',
        'monto_monotr_social',
        'codigo_practica_desde',
        'codigo_practica_hasta',
        'coseguro_vigente',
        'agrupa_coseguro',
        'cod_usuario_registra',
        'monto_sin_coseguro'
    ];
}
