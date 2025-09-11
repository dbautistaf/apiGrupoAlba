<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestChequesEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_test_cheques';
    protected $primaryKey = 'id_cheque';
    public $timestamps = false;

    protected $fillable = [
        'id_cuenta_bancaria',
        'tipo_cheque',
        'numero_cheque',
        'monto',
        'fecha_emision',
        'fecha_vencimiento',
        'tipo',
        'estado',
        'descripcion',
        'archivo_adjunto',
        'cod_usuario_registra',
        'fecha_registra',
        'cod_usuario_modifica',
        'fecha_modificia',
        'beneficiario',
        'numero_cheque_anterior'
    ];

    public function cuenta()
    {
        return $this->hasOne(TesCuentasBancariasEntity::class, 'id_cuenta_bancaria', 'id_cuenta_bancaria');
    }
}
