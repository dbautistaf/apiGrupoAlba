<?php

namespace App\Models\afiliado;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfiliadoLegajoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_discapacidad_legajos';
    protected $primaryKey = 'id_legajo';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_discapacidad',
        'diagnostico',
        'fecha_certificado',
        'fecha_vto',
        'dni_afiliado',
        'certificado',
        'url_adjunto',
        'fecha_registra',
        'cod_usuario_registra',
        'fecha_modifica',
        'cod_usuario_modifica',
        'estado',
        'edad_afiliado'
    ];

    public function afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'dni', 'dni_afiliado');
    }

    public function tipo()
    {
        return $this->hasOne(AfiliadoTipoDiscapacidad::class, 'id_tipo_discapacidad', 'id_tipo_discapacidad');
    }
}
