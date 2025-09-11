<?php

namespace App\Models\RecetarioMedicacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutorizacionRecetarioMedicacionEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_recetarios_medicacion_autorizacion';
    protected $primaryKey = 'cod_auditar';
    public $timestamps = false;

    protected $fillable = [
        'fecha_autorizacion',
        'cod_usuario_audita',
        'observaciones',
        'cod_tipo_rechazo',
        'cod_detalle_receta',
        'estado_autoriza',
        'observacion_auditoria_medica'
    ];


    public function detalle()
    {
        return $this->hasMany(DetalleRecetarioMedicacionEntity::class, 'cod_detalle_receta', 'cod_detalle_receta');
    }
}
