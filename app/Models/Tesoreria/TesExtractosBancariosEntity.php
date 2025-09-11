<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesExtractosBancariosEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_extracto_bancarios';
    protected $primaryKey = 'id_extracto';
    public $timestamps = false;

    protected $fillable = [
        'id_entidad_bancaria',
        'fecha_operacion',
        'fecha_valor',
        'concepto',
        'codigo',
        'num_cheque',
        'oficina',
        'monto_credito',
        'monto_debito',
        'monto_saldo_parcial',
        'monto_saldo_disponible',
        'importe',
        'num_documento',
        'detalle',
        'causal',
        'id_usuario',
        'fecha_registra',
        'observaciones'
    ];

    public function entidadBancaria()
    {
        return $this->hasOne(TesEntidadesBancariasEntity::class, 'id_entidad_bancaria', 'id_entidad_bancaria');
    }
}
