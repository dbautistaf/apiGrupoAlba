<?php

namespace App\Models\Afip;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturasAfipEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_facturacion_afip';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'tipo_comprobante',
        'punto_venta',
        'numero_desde',
        'numero_hasta',
        'cod_autorizacion',
        'tipo_doc_receptor',
        'nro_doc_receptor',
        'denominacion_receptor',
        'tipo_cambio',
        'moneda',
        'imp_neto_gravado',
        'imp_neto_no_gravado',
        'imp_op_exentas',
        'otros_tributos',
        'iva',
        'imp_total',
        'fecha_importacion',
        'cod_usuario'
    ];
}
