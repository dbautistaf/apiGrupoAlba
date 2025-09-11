<?php

namespace App\Models\RecetarioMedicacion;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcepcionesAfiliadoRecetarioEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_recetarios_medicacion_excepcion_afiliados';
    protected $primaryKey = 'id_excepcion';
    public $timestamps = false;

    protected $fillable = [
        'cantidad',
        'periodo',
        'dni_afiliado',
        'observaciones',
        'fecha_registra',
        'id_usuario'
    ];
    public function persona()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'dni', 'dni_afiliado');
    }
}
