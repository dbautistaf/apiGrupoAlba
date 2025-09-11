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
        'fecha_emision',
        'tipo_comprobante',
        'punto_venta',
        'numero_comprobante',
        'tipo_doc_vendedor',
        'nro_doc_vendedor',
        'denominacion_vendedor',
        'importe_total_original',
        'moneda_original',
        'tipo_cambio',
        'importe_no_gravado',
        'importe_externo',
        'credito_fiscal_computable',
        'neto_gravado_iva_5',
        'importe_iva_5',
        'neto_gravado_iva_10_5',
        'importe_iva_10_5',
        'neto_gravado_iva_21',
        'importe_iva_21',
        'neto_gravado_iva_27',
        'importe_iva_27',
        'total_neto_gravado',
        'total_iva',
        'cod_usuario',
        'id_locatario'
    ];
}
