<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleRecetarioEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_detalle_recetario_medicacion';
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
        return $this->hasOne(vademecumModelo::class,'id_vademecum', 'cod_laboratorio');
    }
}
