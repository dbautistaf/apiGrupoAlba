<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesCuentasBloqueadasEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_cuentas_bloqueo';
    protected $primaryKey = 'id_bloqueo';
    public $timestamps = false;

    protected $fillable = [
        'id_cuenta_bancaria',
        'razon_bloqueo',
        'fecha',
        'estado',
        'cod_usuario'
    ];

    public function cuenta()
    {
        return $this->hasOne(TesCuentasBancariasEntity::class, 'id_cuenta_bancaria', 'id_cuenta_bancaria');
    }
}
