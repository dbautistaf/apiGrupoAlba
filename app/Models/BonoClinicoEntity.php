<?php

namespace App\Models;

use App\Models\afiliado\AfiliadoPadronEntity;
use App\Models\prestadores\PrestadorMedicosEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonoClinicoEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_bonos_medicos';
    protected $primaryKey = 'cod_bono';
    public $timestamps = false;

    protected $fillable = [
        'dni_afiliado',
        'fecha_registra',
        'costo_bono',
        'diagnostico',
        'especialidad',
        'observacion',
        'especialidad',
        'vigente',
        'cod_tipo_bono',
        'cod_profesional',
        'cod_usuario_registra'
    ];

    public function afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'dni', 'dni_afiliado');
    }

    public function tipoBono()
    {
        return $this->hasOne(TipoBonoClinicoEntity::class, 'cod_tipo_bono', 'cod_tipo_bono');
    }

    public function medico()
    {
        return $this->hasOne(PrestadorMedicosEntity::class, 'cod_profesional', 'cod_profesional');
    }

    public function usuario()
    {
        return $this->hasOne(User::class,'cod_usuario', 'cod_usuario_registra');
    }
}
