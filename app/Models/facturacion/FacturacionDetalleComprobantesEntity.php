<?php

namespace App\Models\facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacturacionDetalleComprobantesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_facturacion_detalle_comprobantes';
    protected $primaryKey = 'id_comprobante';
    public $timestamps = false;

    use SoftDeletes;

    const DELETED_AT = 'fecha_eliminacion';
    protected $dates = ['fecha_eliminacion'];

    protected $fillable = [
        'archivo',
        'fecha_carga',
        'activo',
        'id_factura'
    ];
}
