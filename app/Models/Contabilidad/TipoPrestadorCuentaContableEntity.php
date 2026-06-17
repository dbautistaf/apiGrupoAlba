<?php

namespace App\Models\Contabilidad;

use App\Models\Prestadores\TipoPrestadorEntity;
use App\Models\proveedor\MatrizProveedoresEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPrestadorCuentaContableEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_cont_tipo_prestador_cuenta_contable';
    protected $primaryKey = 'id_tipo_prestador_cuenta_contable';
    public $timestamps = false;

    protected $fillable = [
        'cod_tipo_prestador',
        'id_detalle_plan',
        'id_razon',
        'cod_usuario_crea',
        'fecha_registra',
        'cod_usuario_modifica',
        'fecha_modifica',
        'vigente'
    ];

    public function tipoPrestador()
    {
        return $this->belongsTo(TipoPrestadorEntity::class, 'cod_tipo_prestador', 'cod_tipo_prestador');
    }

    public function detallePlan()
    {
        return $this->hasOne(DetallePlanCuentasEntity::class, 'id_detalle_plan', 'id_detalle_plan');
    }
}
