<?php

namespace App\Models\liquidacion;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiquidacionObrasSociales extends Model
{
    use HasFactory;
    protected $table = 'tb_liquidaciones_obras';
    protected $primaryKey = 'id_obras';
    public $timestamps = false;

    protected $fillable = [
        'nros',
        'cuil',
        'cuit',
        'periodo_recibido',
        'periodo_devengado',
        'codigo_concepto',
        'nombre_afiliado',
        'trf_total',
        'fecha_proceso',
    ];

    public function PadronAfil()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'cuil_benef', 'cuil');
    }
}
