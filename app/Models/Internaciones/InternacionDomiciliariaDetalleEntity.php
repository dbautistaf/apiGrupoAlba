<?php

namespace  App\Models\Internaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternacionDomiciliariaDetalleEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_internaciones_domiciliaria_detalle';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;
    protected $fillable = [
        'id_servicio',
        'cantidad',
        'observaciones',
        'id_internacion_domiciliaria'
    ];
    public function servicio()
    {
        return $this->hasOne(InternacionDomiciliariaServiciosEntity::class, 'id_servicio', 'id_servicio');
    }

}
