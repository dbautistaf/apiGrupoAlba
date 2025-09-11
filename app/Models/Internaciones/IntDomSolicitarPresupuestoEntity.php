<?php

namespace  App\Models\Internaciones;

use App\Models\prestadores\PrestadorEntity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntDomSolicitarPresupuestoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_internaciones_domiciliaria_solicitar_presupuesto';
    protected $primaryKey = 'id_solicitud';
    public $timestamps = false;
    protected $fillable = [
        'id_internacion_domiciliaria',
        'cod_prestador',
        'fecha_solicita_presupuesto',
        'cod_usuario',
        'gano_licitacion',
        'fecha_registra_ganador',
        'cod_usuario_registra_ganador',
        'archivo_cotizacion'
    ];

    public function prestador()
    {
        return $this->hasOne(PrestadorEntity::class, 'cod_prestador', 'cod_prestador');
    }
    public function usuario()
    {
        return $this->hasOne(User::class, 'cod_usuario', 'cod_usuario_registra_ganador');
    }
}
