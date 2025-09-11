<?php

namespace App\Models\Pmi;

use App\Models\afiliado\AfiliadoPadronEntity;
use App\Models\PadronModelo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmiModel extends Model
{
    use HasFactory;
    protected $table = 'tb_pmi';
    protected $primaryKey = 'id_pmi';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_embarazo',
        'observaciones',
        'fecha_alta',
        'fecha_baja',
        'fecha_carga',
        'id_usuario',
        'dni_afiliado',
        'url_adjunto'
    ];

    public function afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'dni', 'dni_afiliado');
    }

    public function embarazo()
    {
        return $this->hasOne(TipoEmbarazoModel::class, 'id_tipo_embarazo', 'id_tipo_embarazo');
    }
}
