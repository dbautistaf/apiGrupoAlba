<?php

namespace App\Models\Diabetes;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiabetesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_padron_diabetes';
    protected $primaryKey = 'id_diabetes';
    public $timestamps = false;

    protected $fillable = [
        'dni_afiliado',
        'id_tipo_diabetes',
        'fecha_alta',
        'fecha_baja',
        'observaciones',
        'cod_usuario_registra',
        'fecha_registra',
        'cod_usuario_modifica',
        'fecha_modifica',
        'vigente',
        'fecha_anula',
        'cod_usuario_anula',
        'id_padron'
    ];

    public function afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'id', 'id_padron');
    }

    public function tipoDiabetes()
    {
        return $this->hasOne(TipoDiabetesEntity::class, 'id_tipo_diabetes', 'id_tipo_diabetes');
    }

    public function detalle()
    {
        return $this->hasMany(DetalleDiabetesEntity::class, 'id_diabetes', 'id_diabetes');
    }
}
