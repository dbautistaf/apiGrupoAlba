<?php

namespace App\Models\Tesoreria;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesOperacionesManualesEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_operaciones_manuales';
    protected $primaryKey = 'id_operacion';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_transaccion',
        'fecha_operacion',
        'id_cuenta_bancaria',
        'monto_operacion',
        'id_tipo_moneda',
        'observaciones',
        'comprobante',
        'estado_operacion',
        'id_usuario',
        'fecha_registra',
        'id_usuario_modifica',
        'fecha_modifica',
        'motivo_anulacion',
        'id_cuenta_bancaria_destino',
        'monto_retencion',
        'num_factura'
    ];

    public function cuenta()
    {
        return $this->hasOne(TesCuentasBancariasEntity::class, 'id_cuenta_bancaria', 'id_cuenta_bancaria');
    }

    public function destino()
    {
        return $this->hasOne(TesCuentasBancariasEntity::class, 'id_cuenta_bancaria', 'id_cuenta_bancaria_destino');
    }
    public function tipoMoneda()
    {
        return $this->hasOne(TesTipoMonedasEntity::class, 'id_tipo_moneda', 'id_tipo_moneda');
    }
    public function transaccion()
    {
        return $this->hasOne(TesTipoTransaccionEntity::class, 'id_tipo_transaccion', 'id_tipo_transaccion');
    }

    public function usuario()
    {
        return $this->hasOne(User::class, 'cod_usuario', 'id_usuario');
    }
}
