<?php

namespace App\Models\Fiscalizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcuerdoPagoPeriodo extends Model
{
    use HasFactory;

    protected $table = 'tb_fisca_acuerdo_pago_periodo';
    protected $primaryKey = 'id_acuerdo_pago_periodo';
    public $timestamps = false;

    protected $fillable = [
        'id_acuerdo_pago',
        'id_periodo',
        'monto_asociado'
    ];

    public function acuerdoPago()
    {
        return $this->belongsTo(AcuerdoPago::class, 'id_acuerdo_pago', 'id_acuerdo_pago');
    }

    public function periodo()
    {
        return $this->hasOne(Periodo::class, 'id_periodo', 'id_periodo');
    }
}
