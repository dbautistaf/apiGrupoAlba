<?php

namespace   App\Models\PrestacionesMedicas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPrioridadAutorizacionesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_prestacion_medica_tipo_prioridad';
    protected $primaryKey = 'id_tipo_prioridad';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'estado'
    ];
}
