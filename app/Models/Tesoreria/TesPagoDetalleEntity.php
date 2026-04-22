<?php

namespace App\Models\Tesoreria;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesPagoDetalleEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_pago_detalle';
    protected $primaryKey = 'id_pago_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_pago',
        'id_forma_pago',
        'monto',
        'id_cuenta_bancaria',
        'num_cheque',
        'id_chequera',
        'fecha_acreditacion',
        'observaciones',
        'cod_usuario',
        'fecha_registra',
        'nro_transferencia',
        'fecha_transferencia'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_acreditacion' => 'date',
        'fecha_registra' => 'datetime',
        'fecha_transferencia' => 'datetime'
    ];

    public function pago()
    {
        return $this->belongsTo(TesPagoEntity::class, 'id_pago', 'id_pago');
    }

    public function formaPago()
    {
        return $this->belongsTo(TesTipoFormasPagoEntity::class, 'id_forma_pago', 'id_forma_pago');
    }

    public function cuenta()
    {
        return $this->belongsTo(TesCuentasBancariasEntity::class, 'id_cuenta_bancaria', 'id_cuenta_bancaria');
    }

    public function chequera()
    {
        return $this->belongsTo(ChequerasCuentasBancariasEntity::class, 'id_chequera', 'id_chequera');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'cod_usuario', 'cod_usuario');
    }

    public function cheque()
    {
        return $this->hasOne(TestChequesEntity::class, 'numero_cheque', 'num_cheque');
    }

}
