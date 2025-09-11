<?php

namespace  App\Models\RecetarioMedicacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleRecetarioMedicacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_recetarios_medicacion_detalle';
    protected $primaryKey = 'cod_detalle_receta';
    public $timestamps = false;

    protected $fillable = [
        'cod_receta',
        'cantidad_autoriza',
        'cantidad_solicita',
        'estado_autoriza',
        'cod_laboratorio',
        'diagnostico',
        'cod_tipo_troquel'
    ];

    public function laboratorio()
    {
        return $this->hasOne(VademecumRecetarioMedicacionEntity::class, 'id_vademecum', 'cod_laboratorio');
    }
}
