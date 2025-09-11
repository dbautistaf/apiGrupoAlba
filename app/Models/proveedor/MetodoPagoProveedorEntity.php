<?php

namespace App\Models\proveedor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetodoPagoProveedorEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_proveedor_metodo_pago';
    protected $primaryKey = 'id_pago_proveedor';
    public $timestamps = false;

    protected $fillable = [
        'cod_proveedor',
        'id_tipo_metodo_pago',
        'dia_corte_mensual',
        'dia_pago_antes_vencimiento',
        'dia_pago_despues_vencimiento'
    ];
}
