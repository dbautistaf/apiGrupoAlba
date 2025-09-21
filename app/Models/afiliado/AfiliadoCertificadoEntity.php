<?php

namespace App\Models\afiliado;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfiliadoCertificadoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_discapacidad_certificado';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_discapacidad',
        'diagnostico',
        'fecha_certificado',
        'fecha_vto',
        'id_padron',
        'certificado',
        'url_adjunto',
        'fecha_registra',
        'cod_usuario_registra',
        'fecha_modifica',
        'cod_usuario_modifica'
    ];

    public function afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'dni', 'id_padron');
    }

    public function tipo()
    {
        return $this->hasOne(AfiliadoTipoDiscapacidad::class, 'id_tipo_discapacidad', 'id_tipo_discapacidad');
    }
    
}
