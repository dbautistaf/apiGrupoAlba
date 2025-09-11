<?php

namespace App\Models\afiliado;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramaEspecialAfiEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_afiliado_programa_especial';
    protected $primaryKey = 'id_programa';
    public $timestamps = false;

    use SoftDeletes;
    const DELETED_AT = 'fecha_elimina';
    protected $fillable = [
        'dni_afiliado',
        'id_tipo_programa_especial',
        'fecha_alta',
        'fecha_baja',
        'estado_clinico',
        'medico_tratante',
        'medico_especialidad',
        'medico_telefono',
        'medico_email',
        'observaciones',
        'documento_adjunto',
        'fecha_registra',
        'cod_usuario_registra',
        'fecha_modifica',
        'cod_usuario_modifica',
        'fecha_elimina',
        'estado_tramite'
    ];

    public function afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'dni', 'dni_afiliado');
    }

    public function tipo_programa()
    {
        return $this->hasOne(TipoProgramaEspecialAfiEntity::class, 'id_tipo_programa_especial', 'id_tipo_programa_especial');
    }
}
