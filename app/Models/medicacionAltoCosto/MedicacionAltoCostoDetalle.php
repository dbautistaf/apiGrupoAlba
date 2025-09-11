<?php

namespace App\Models\medicacionAltoCosto;

use App\Models\afiliado\AfiliadoTipoCoberturaEntity;
use App\Models\RecetarioMedicacion\VademecumRecetarioMedicacionEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicacionAltoCostoDetalle extends Model
{
    use HasFactory;
    protected $table = 'tb_medicacion_alto_costo_detalle';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_medicacion_alto_costo',
        'id_vademecum',
        'cantidad',
        'id_cobertura',
        'precio_unitario',
        'precio_total',
        'fecha_registro',
        'estado_registro'
    ];

    public function producto()
    {
        return $this->belongsTo(VademecumRecetarioMedicacionEntity::class, 'id_vademecum');
    }

    public function cobertura()
    {
        return $this->hasOne(AfiliadoTipoCoberturaEntity::class, 'id_cobertura', 'id_cobertura');
    }

    // public function MedicacionAltoCosto()
    // {
    //     return $this->belongsTo(MedicacionAltoCosto::class, 'id_medicacion_alto_costo');
    // }
}
