<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditarPrestacionesPracticaLaboratorioEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_prestaciones_medicas_autorizadas';
    protected $primaryKey = 'cod_auditar';
    public $timestamps = false;

    protected $fillable = [
        'fecha_autorizacion',
        'cod_usuario_audita',
        'observaciones',
        'cod_tipo_rechazo',
        'cod_detalle',
        'estado_autoriza',
        'cod_recetario'
    ];
}
