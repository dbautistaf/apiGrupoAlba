<?php

namespace   App\Models\Internaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternacionDomiciliariaServiciosEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_internaciones_domiciliaria_servicios';
    protected $primaryKey = 'id_servicio';
    public $timestamps = false;

    protected $fillable = [
        'tipo_servicio',
        'frecuencia',
        'duracion',
        'costo_unitario'
    ];
}
