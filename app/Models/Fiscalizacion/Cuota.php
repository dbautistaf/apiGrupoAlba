<?php

namespace App\Models\Fiscalizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuota extends Model
{
    use HasFactory;

    protected $table = 'tb_fisca_cuotas';
    protected $primaryKey = 'id_cuota';
    public $timestamps = false;

    protected $fillable = [
        'id_acuerdo_pago',
        'numero_cuota',
        'importe_cuota',
        'capital_cuota',
        'interes_cuota',
        'fecha_pago',
        'fecha_vencimiento',
        'forma_pago',
        'comprobante',
        'estado'
    ];

    public function acuerdoPago()
    {
        return $this->belongsTo(AcuerdoPago::class, 'id_acuerdo_pago', 'id_acuerdo_pago');
    }

    public function archivos()
    {
        return $this->hasMany(ArchivoCuotas::class, 'id_cuota');
    }
}
