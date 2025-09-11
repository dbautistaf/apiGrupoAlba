<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutorizacionDetalleModel extends Model
{
    use HasFactory;
    protected $table = 'tb_autorizacion_detalle';
    protected $primaryKey = 'cod_detalle';
    public $timestamps = false;

    protected $fillable = [
        'cantidad_solicitada',
        'cantidad_autorizada',
        'precio_unitario',
        'monto_pagar',
        'cod_tipo_practica',
        'id_autorizacion',
        'codigo_valor_practica',
        'cod_prestacion',
    ];

    public function autorizacion()
    {
        return $this->belongsTo(AutorizacionModels::class, 'id_autorizacion'); 
    }
}
