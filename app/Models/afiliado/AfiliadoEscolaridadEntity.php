<?php

namespace App\Models\Afiliado;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfiliadoEscolaridadEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_escolaridad';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nivel_estudio',
        'fecha_presentacion',
        'fecha_vencimiento',
        'id_padron',
        'url_adjunto',
        'fecha_registra',
        'cod_usuario_registra',
        'fecha_modifica',
        'cod_usuario_modifica'
    ];

    public function afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'id', 'id_padron');
    }
}
