<?php

namespace App\Models\Discapacidad;

use App\Models\IntegracionDiscapacidadModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscapacidadTesoreriaEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_dispacidad_tesoreria';
    protected $primaryKey = 'id_discapacidad_tesoreria';
    public $timestamps = false;

    protected $fillable = [
        'id_discapacidad',
        'cuit_prestador',
        'cbu',
        'orden_pago_1',
        'orden_pago_2',
        'fecha_transferencia_1',
        'fecha_transferencia_2',
        'cheque',
        'importe_transferido',
        'retencion_ganancias',
        'retencion_ingresos_brutos',
        'otras_retenciones',
        'importe_aplicado_sss',
        'fondos_propios_cuenta_discapacidad',
        'fondos_propios_otra_cuenta',
        'numero_recibo',
        'importe_reversion',
        'importe_devuelto_cuenta_sss',
        'saldo_no_aplicado',
        'recupero_fondos_propios',
        'diferencia',
        'fecha_proceso',
        'id_usuario',
        'observaciones'
    ];

    public function disca()
    {
        return $this->belongsTo(IntegracionDiscapacidadModel::class, 'id_discapacidad', 'id_discapacidad');
    }
}
