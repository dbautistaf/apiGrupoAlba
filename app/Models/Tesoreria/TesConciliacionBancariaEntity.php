<?php

namespace App\Models\Tesoreria;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesConciliacionBancariaEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_conciliacion_bancaria';
    protected $primaryKey = 'id_conciliacion';
    public $timestamps = false;

    protected $fillable = [
        'id_cuenta_bancaria',
        'monto_cuenta',
        'monto_contabilidad',
        'id_usuario',
        'fecha_registra',
        'id_usuario_modifica',
        'fecha_modifica',
        'observaciones'
    ];

    public function cuenta()
    {
        return $this->hasOne(TesCuentasBancariasEntity::class, 'id_cuenta_bancaria', 'id_cuenta_bancaria');
    }

    public function usuario()
    {
        return $this->hasOne(User::class, 'cod_usuario', 'id_usuario');
    }
}
