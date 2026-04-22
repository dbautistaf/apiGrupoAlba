<?php

namespace App\Models\Tesoreria;

use App\Models\facturacion\FacturacionDatosEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesFacturasOpaEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_opa_factura';
    protected $primaryKey = 'id_op_factura';

    protected $fillable = [
        'id_orden_pago',
        'id_factura',
        'monto_aplicado',
        'fecha_imputacion',
        'cod_usuario'
    ];

    protected $casts = [
        'monto_aplicado' => 'decimal:2',
        'fecha_imputacion' => 'datetime'
    ];

    public $timestamps = false;

    public function ordenPago()
    {
        return $this->belongsTo(TesOrdenPagoEntity::class, 'id_orden_pago', 'id_orden_pago');
    }

    public function factura()
    {
        return $this->belongsTo(FacturacionDatosEntity::class, 'id_factura', 'id_factura');
    }
}
