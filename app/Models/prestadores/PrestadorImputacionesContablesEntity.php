<?php

namespace App\Models\prestadores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestadorImputacionesContablesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_prestador_detalle_imputaciones_contables';
    protected $primaryKey = 'id_imputacion_prestador';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_imputacion_contable',
        'cod_prestador',
        'fecha_carga',
        'cod_usuario_carga',
        'fecha_modifica',
        'cod_usuario_modifica',
        'clasificacion',
        'vigente'
    ];

    public function imputacion()
    {
        return $this->hasOne(TipoInputacionesContablesEntity::class, 'id_tipo_imputacion_contable', 'id_tipo_imputacion_contable');
    }

    public function prestador()
    {
        return $this->hasOne(PrestadorEntity::class, 'cod_prestador', 'cod_prestador');
    }
}
