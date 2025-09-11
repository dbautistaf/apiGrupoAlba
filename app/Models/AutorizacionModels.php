<?php

namespace App\Models;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutorizacionModels extends Model
{
    use HasFactory;
    protected $table = 'tb_autorizacion_solicitudes';
    protected $primaryKey = 'id_autorizacion';
    public $timestamps = false;

    protected $fillable = [
        'tipo_autorizacion',
        'fecha_pedido',
        'fecha_autorizacion',
        'dni',
        'estado',
        'motivo',
        'observaciones',
        'observacion_auditoria',
        'url',
        'cuil_tit',
        'id_usuario',
    ];

    public function padron()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'dni', 'dni');
    }

    public function detalles()
    {
        return $this->hasMany(AutorizacionDetalleModel::class, 'id_autorizacion');
    }
}
