<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanesCuentaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_cont_planes_cuentas';
    protected $primaryKey = 'id_plan_cuenta';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_plan_cuenta',
        'plan_cuenta',
        'cod_usuario_crea',
        'fecha_registra',
        'cod_usuario_modifica',
        'fecha_modifica',
        'activo'
    ];


    public function tipo()
    {
        return $this->hasOne(TipoPlanCuentaEntity::class, 'id_tipo_plan_cuenta', 'id_tipo_plan_cuenta');
    }
}
