<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesMovientosCuentaBancariaEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_movimiento_cuenta_bancaria';
    protected $primaryKey = 'id_movimiento';
    public $timestamps = false;

    protected $fillable = [
        'id_cuenta_bancaria',
        'id_usuario',
        'fecha_movimiento',
        'monto',
        'tipo_movimiento',
        'id_pago',
        'descripcion',
        'id_operacion'
    ];

    public function cuenta()
    {
        return $this->hasOne(TesCuentasBancariasEntity::class, 'id_cuenta_bancaria', 'id_cuenta_bancaria');
    }
}
