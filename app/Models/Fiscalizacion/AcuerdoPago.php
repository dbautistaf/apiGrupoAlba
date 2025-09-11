<?php

namespace App\Models\Fiscalizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EmpresaModelo;

class AcuerdoPago extends Model
{
    use HasFactory;

    protected $table = 'tb_fisca_acuerdo_pago';
    protected $primaryKey = 'id_acuerdo_pago';
    public $timestamps = false;

    protected $fillable = [
        'id_empresa',
        'id_estado_acuerdo',
        'numero_acta',
        'fecha_creacion',
        'importe_total',
        'usuario',
        'id_expediente'

    ];

    // Relaciones
    public function empresa()
    {
        return $this->belongsTo(EmpresaModelo::class, 'id_empresa', 'id_empresa');
    }

    public function estado()
    {
        return $this->hasOne(EstadoAcuerdo::class, 'id_estado_acuerdo', 'id_estado_acuerdo');
    }

    public function cuotas()
    {
        return $this->hasMany(Cuota::class, 'id_acuerdo_pago', 'id_acuerdo_pago');
    }

    public function periodos()
    {
        return $this->hasMany(AcuerdoPagoPeriodo::class, 'id_acuerdo_pago', 'id_acuerdo_pago');
    }

    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'id_expediente', 'id_expediente');
    }
}
