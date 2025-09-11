<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConvenioHistorialCostosPracticaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_historial_costos';
    protected $primaryKey = 'id_historial_pago';
    public $timestamps = false;

    protected $fillable = [
        'id_identificador_practica',
        'cod_convenio',
        'monto_especialista',
        'monto_gastos',
        'monto_ayudante',
        'vigente',
        'tipo_carga',
        'fecha_inicio',
        'fecha_fin',
        'fecha_update',
        'cod_usuario_crea',
        'cod_usuario_update',
        'valor_aumento_lineal',
        'tipo_aumento',
        'observaciones'
    ];
}
