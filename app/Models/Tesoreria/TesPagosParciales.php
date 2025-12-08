<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesPagosParciales extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_pago_parcial';
    protected $primaryKey = 'id_pago_parcial';
    public $timestamps = false;

    protected $fillable = [
        'fecha_registra',
        'fecha_confirma_pago',
        'id_forma_pago',
        'monto_pago',
        'id_usuario',
        'monto_opa',
        'num_cheque',
        'id_pago',
        'monto_restante',
    ];

    public function formaPago()
    {
        return $this->hasOne(TesTipoFormasPagoEntity::class, 'id_forma_pago', 'id_forma_pago');
    }
}
