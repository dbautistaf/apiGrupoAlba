<?php

namespace App\Models\facturacion;

use App\Models\articulos\ArticuloMatrizEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturacionDetalleEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_facturacion_detalle';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_articulo',
        'cantidad',
        'precio_neto',
        'iva',
        'subtotal',
        'monto_iva',
        'total_importe',
        'id_factura',
        'id_tipo_iva',
        'observaciones'
    ];

    public function articulo()
    {
        return $this->hasOne(ArticuloMatrizEntity::class, 'id_articulo', 'id_articulo');
    }
}
