<?php

namespace App\Models\facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoComprobanteFacturacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_facturacion_tipo_comprobantes';
    protected $primaryKey = 'id_tipo_comprobante';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
