<?php

namespace App\Models\arca;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelacionLaboralModel extends Model
{
    use HasFactory;
    protected $table = 'tb_relaciones_laborales';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'cuit_empleador',
        'cuil_empleado',
        'apellido_nombre',
        'fecha_inicio_relacion',
        'fecha_fin_relacion',
        'codigo_obra_social',
        'clave_alta_registro',
        'fecha_clave_alta',
        'separador1',
        'hora_clave_alta',
        'clave_baja_registro',
        'fecha_clave_baja',
        'separador2',
        'hora_clave_baja',
        'codigo_modalidad_contrato',
        'trabajador_agropecuario',
        'regimen_aportes',
        'codigo_situacion_baja',
        'filler1',
        'fecha_movimiento',
        'separador3',
        'hora_movimiento',
        'codigo_movimiento',
        'remuneracion_bruta',
        'codigo_modalidad_liquidacion',
        'codigo_sucursal_explotacion',
        'codigo_actividad',
        'codigo_puesto_desempenado',
        'fecha_telegrama_renuncia',
        'filler2',
        'marca_rectificacion',
        'numero_formulario_agropecuario',
        'tipo_servicio',
        'codigo_categoria_profesional',
        'cct',
        'area_reservada',
        'fecha_proceso',
        'id_usuario',
    ];
}
