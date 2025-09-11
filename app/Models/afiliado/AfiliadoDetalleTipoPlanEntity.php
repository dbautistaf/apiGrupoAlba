<?php

namespace App\Models\afiliado;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfiliadoDetalleTipoPlanEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_detalle_padron_tipo_plan';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'fecha_alta',
        'fecha_baja',
        'id_tipo_plan',
        'id_padron'
    ];

    public function TipoPlan()
    {
        return $this->belongsTo(AfiliadoTipoPlanEntity::class, 'id_tipo_plan', 'id_tipo_plan');
    }

    public function addplan()
    {
        return $this->hasOne(AfiliadoTipoPlanEntity::class, 'id_tipo_plan', 'id_tipo_plan');
    }

}
