<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestDetalleComprobantesPagoEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_test_pago_detalle_comprobantes';
    protected $primaryKey = 'id_comprobante';
    public $timestamps = false;
    protected $fillable = [
        'id_pago',
        'nombre_archivo',
        'fecha_registra',
        'cod_usuario_registra',
        'cod_usuario_elimina',
        'fecha_elimina',
        'estado'
    ];

    protected $hidden = [
        'id_pago',
        'cod_usuario_registra',
        'cod_usuario_elimina',
        'fecha_elimina',
        'estado'
    ];
}
