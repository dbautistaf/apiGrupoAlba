<?php

namespace App\Models\Proveedor;

use App\Models\prestadores\TipoInputacionesContablesEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImputacionProveedorEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_proveedor_detalle_imputaciones_contables';
    protected $primaryKey = 'id_imputacion_proveedor';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_imputacion_contable',
        'cod_proveedor',
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
        return $this->hasOne(MatrizProveedoresEntity::class, 'cod_proveedor', 'cod_proveedor');
    }
}
