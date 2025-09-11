<?php

namespace  App\Models\Derivaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitarPresupuestosDerivacionEntity extends Model
{

    use HasFactory;
    protected $table = 'tb_derivacion_solicitar_presupuestos';
    protected $primaryKey = 'id_solicitud';
    public $timestamps = false;

    protected $fillable = [
        'id_derivacion',
        'cod_prestador',
        'fecha_solicita_presupuesto',
        'cod_usuario',
        'gano_licitacion',
        'fecha_registra_ganador',
        'cod_usuario_registra_ganador',
        'archivo_cotizacion',
        'monto_cotiza',
        'observaciones'
    ];
}
