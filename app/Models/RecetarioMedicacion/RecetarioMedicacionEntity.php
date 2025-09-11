<?php

namespace App\Models\RecetarioMedicacion;

use App\Models\afiliado\AfiliadoPadronEntity;
use App\Models\PrestacionesMedicas\TipoEstadoPrestacionEntity;
use App\Models\prestadores\PrestadorMedicosEntity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//RecetariosEntity
class RecetarioMedicacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_recetarios_medicacion';
    protected $primaryKey = 'cod_receta';
    public $timestamps = false;

    protected $fillable = [
        'fecha_registra',
        'cod_profesional',
        'dni_afiliado',
        'estado_impresion',
        'cod_tipo_estado',
        'usuario_registra',
        'usuario_imprime',
        'vigente',
        'fecha_impresion',
        'edad_afiliado',
        'fecha_solicita',
        'numero_receta',
        'fecha_vencimiento'
    ];

    public function afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'dni', 'dni_afiliado');
    }

    public function medico()
    {
        return $this->hasOne(PrestadorMedicosEntity::class, 'cod_profesional', 'cod_profesional');
    }

    public function estadoPrestacion()
    {
        return $this->hasOne(TipoEstadoPrestacionEntity::class, 'cod_tipo_estado', 'cod_tipo_estado');
    }

    public function usuario()
    {
        return $this->hasOne(User::class, 'cod_usuario', 'usuario_registra');
    }

    public function detalle()
    {
        return $this->hasMany(DetalleRecetarioMedicacionEntity::class, 'cod_receta', 'cod_receta');
    }
}
