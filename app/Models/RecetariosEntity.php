<?php

namespace App\Models;

use App\Models\afiliado\AfiliadoPadronEntity;
use App\Models\prestadores\PrestadorMedicosEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecetariosEntity extends Model
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
        return $this->hasOne(User::class,'cod_usuario', 'usuario_registra');
    }

    public function detalle()
    {
        return $this->hasMany(DetalleRecetarioEntity::class,'cod_receta', 'cod_receta');
    }
}
