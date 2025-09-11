<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleRecetasModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_recetas_detalle';
    protected $primaryKey = 'id_detalle_receta';
    public $timestamps = false;

    protected $fillable = [
        'id_vademecum',
        'cantidad',
        'valor_unitario',
        'valor_total',
        'afiliado_total',
        'cargo_osyc',
        'total_obra_social',
        'importe_total',        
        'venta_publico',
        'diabetes',
        'recupero',
        'pmi',
        'id_receta',
        'id_cobertura'
    ];

    public function vademecum()
    {
        return $this->hasOne(vademecumModelo::class,'id_vademecum', 'id_vademecum');
    }
}
