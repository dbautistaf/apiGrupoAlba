<?php

namespace App\Models\Protesis;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProtesisDetalleEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_protesis_detalle';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_protesis',
        'id_producto',
        'cantidad_solicita',
        'cantidad_autoriza',
        'cobertura',
        'tiene_recupero',
        'observaciones',
        'id_programa_especial',
        'id_origen_material',
        'id_tipo_cobertura',
        'pmo',
        'coseguro'
    ];

    public function producto()
    {
        return $this->hasOne(ProtesisMatrizProductosEntity::class, 'id_producto', 'id_producto');
    }

    public function cobertura()
    {
        return $this->hasOne(TipoCoberturaProtesisEntity::class, 'id_tipo_cobertura', 'id_tipo_cobertura');
    }

    public function programa()
    {
        return $this->hasOne(ProgramaEspecialProtesisEntity::class, 'id_programa_especial', 'id_programa_especial');
    }

    public function material()
    {
        return $this->hasOne(OrigenMaterialProtesisEntity::class, 'id_origen_material', 'id_origen_material');
    }
}
