<?php

namespace App\Models\Afip;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComprobantesAfipCompraEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_comprobantes_afip_compras';
    protected $primaryKey = 'id_comprobante_afip';
    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'tipo_comprobante',
        'punto_venta',
        'numero_desde',
        'numero_hasta',
        'cod_autorizacion',
        'tipo_doc_emisor',
        'nro_doc_emisor',
        'denominacion_emisor',
        'tipo_doc_receptor',
        'nro_doc_receptor',
        'tipo_cambio',
        'moneda',
        'neto_gravado_iva_0',
        'iva_25',
        'neto_gravado_iva_25',
        'iva_5',
        'neto_gravado_iva_5',
        'iva_105',
        'neto_gravado_iva_105',
        'iva_21',
        'neto_gravado_iva_21',
        'iva_27',
        'neto_gravado_iva_27',
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
