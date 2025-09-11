<?php

namespace  App\Models\SuperIntendencia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BajasMonotributoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_bajas_monotributo';
    protected $primaryKey = 'id_baja_monotributo';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'formulario',
        'rnos',
        'cuil_titular',
        'nombres',
        'telefono1',
        'telefono2',
        'calle',
        'altura',
        'piso',
        'departamento',
        'coigo_postal',
        'obra_social_elegida',
        'localidad',
        'provincia',
        'cuit_empresa',
        'empresa',
        'rnos_destino',
        'fecha_vigencia',
        'periodo',
        'fecha_importacion',
        'email',
        'campo_1',
        'campo_2',
        'campo_3',
        'cod_usuario_registra',
        'fecha_registra'
    ];
}
