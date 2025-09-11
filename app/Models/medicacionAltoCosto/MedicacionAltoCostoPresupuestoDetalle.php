<?php

namespace App\Models\medicacionAltoCosto;

use App\Models\RecetarioMedicacion\VademecumRecetarioMedicacionEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicacionAltoCostoPresupuestoDetalle extends Model
{
    use HasFactory;
    protected $table = 'tb_medicacion_alto_costo_presupuesto_detalles';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;
    protected $fillable = [
        'id_presupuesto',
        'id_vademecum',
        'cantidad',
        'precio_unitario',
        'precio_total'
    ];

    public function producto()
    {
        return $this->belongsTo(VademecumRecetarioMedicacionEntity::class, 'id_vademecum');
    }
}
