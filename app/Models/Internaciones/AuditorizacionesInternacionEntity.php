<?php

namespace   App\Models\Internaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class   AuditorizacionesInternacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_internaciones_auditadas';
    protected $primaryKey = 'id_autorizacion';
    public $timestamps = false;

    protected $fillable = [
        'cod_internacion',
        'fecha_autoriza',
        'cod_tipo_estado',
        'cod_usuario',
        'observaciones',
        'dias_autoriza'
    ];
}
