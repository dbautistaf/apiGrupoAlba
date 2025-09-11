<?php

namespace App\Models\Reclamos;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReclamosModel extends Model
{
    use HasFactory;
    protected $table = 'tb_reclamos';
    protected $primaryKey = 'id_reclamo';
    public $timestamps = false;

    protected $fillable = [
        'dni_afiliado',
        'fecha_Reclamo',
        'tipo_reclamo',
        'detalle_Reclamo',
        'detalle_respuesta',
        'estado_reclamo',
        'fecha_respuesta',
        'id_usuario',
        'id_tipo_reclamo',
    ];

    public function padron()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'dni', 'dni_afiliado');
    }

    public function tiporeclamo()
    {
        return $this->hasOne(TipoReclamosModel::class, 'id_tipo_reclamo', 'id_tipo_reclamo');
    }
}
