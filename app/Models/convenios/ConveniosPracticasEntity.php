<?php

namespace App\Models\convenios;

use App\Models\configuracion\TipoPlanGalenosEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConveniosPracticasEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_practicas';
    protected $primaryKey = 'id_practica_convenio';
    public $timestamps = false;

    protected $fillable = [
        'id_identificador_practica',
        'cod_convenio',
        'monto_especialista',
        'monto_gastos',
        'monto_ayudante',
        'vigente',
        'tipo_carga',
        'fecha_vigencia',
        'fecha_carga',
        'cod_usuario_carga',
        'fecha_vigencia_hasta',
        'valor_aumento_lineal',
        'por_recaudacion',
        'observaciones'
    ];
}
