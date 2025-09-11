<?php

namespace   App\Models\Internaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternacionDomiciliariaHistorialCostoEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_internaciones_domiciliaria_historial_costos';
    protected $primaryKey = 'id_historial';
    public $timestamps = false;

    protected $fillable = [
        'id_servicio',
        'fecha_ajuste',
        'monto_anterior',
        'monto_nuevo',
        'cod_usuario',
        'id_internacion_domiciliaria'
    ];
    public function servicio()
    {
        return $this->hasOne(InternacionDomiciliariaServiciosEntity::class, 'id_servicio', 'id_servicio');
    }
}

