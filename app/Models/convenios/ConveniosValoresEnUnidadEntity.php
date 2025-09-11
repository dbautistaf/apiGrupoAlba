<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConveniosValoresEnUnidadEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_valores_unidades';
    protected $primaryKey = 'cod_valores_unidades';
    public $timestamps = false;

    protected $fillable = [
        'cod_tipo_unidad',
        'monto_honorarios',
        'monto_gastos',
        'fecha_inicio',
        'fecha_fin',
        'cod_usuario',
        'fecha_crea',
        'cod_convenio'
    ];

    /* public function tipoUnidad()
    {
        return $this->hasOne(ConveniosTipoUnidadesEntity::class, 'cod_tipo_unidad', 'cod_tipo_unidad');
    } */
}
