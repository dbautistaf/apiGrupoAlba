<?php

namespace App\Models\afiliado;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfiliadoCronicoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_cronicos';
    protected $primaryKey = 'id_cronico';
    public $timestamps = false;

    protected $fillable = [
        'id_patologia',
        'observaciones',
        'fecha_alta',
        'fecha_baja',
        'fecha_carga',
        'id_usuario',
        'id_padron',
        'fecha_modifica',
        'cod_usuario_modifica'
    ];

    public function afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'id', 'id_padron');
    }
    public function patologia()
    {
        return $this->hasOne(AfiliadoTipoPatalogiaEntity::class, 'id_patologia', 'id_patologia');
    }
}
