<?php

namespace  App\Models\SuperIntendencia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AltasMonotributoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_altas_monotributo';
    protected $primaryKey = 'id_altas_monotributo';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'tipo',
        'rnos',
        'cuil_titular',
        'nombres',
        'telefono',
        'telefono_2',
        'calle',
        'altura',
        'piso',
        'dpto',
        'extra',
        'codigo_postal',
        'localidad',
        'provincia',
        'cuit_empresa',
        'empresa',
        'obra_social_origen',
        'fecha_vigencia',
        'periodo',
        'rnos_origen',
        'fecha_importacion',
        'email',
        'cod_usuario_registra',
        'fecha_registra'
    ];
}
