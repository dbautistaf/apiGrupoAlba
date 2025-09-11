<?php

namespace App\Models\articulos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticuloMatrizEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_articulos_matriz';
    protected $primaryKey = 'id_articulo';
    public $timestamps = false;

    protected $fillable = [
        'id_familia',
        'id_subfamilia',
        'id_rubro',
        'id_unidad_medida',
        'descripcion_articulo',
        'codigo_barra',
        'fraccionable',
        'por_lote',
        'promedio',
        'ultima_compra',
        'empresa',
        'vigente',
        'fecha_registra',
        'fecha_actualiza',
        'cod_usuario',
        'id_tipo_imputacion'
    ];


    public function familia()
    {
        return $this->hasOne(ArticuloFamiliaEntity::class, 'id_familia', 'id_familia');
    }
    public function subfamilia()
    {
        return $this->hasOne(ArticuloSubfamiliaEntity::class, 'id_subfamilia', 'id_subfamilia');
    }
    public function rubro()
    {
        return $this->hasOne(ArticuloRubrosEntity::class, 'id_rubro', 'id_rubro');
    }

    public function unidadmedida()
    {
        return $this->hasOne(ArticuloUnidadMedidaEntity::class, 'id_unidad_medida', 'id_unidad_medida');
    }
}
