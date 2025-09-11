<?php

namespace App\Models\afiliado;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfiliadoTipoPlanEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_plan';
    protected $primaryKey = 'id_tipo_plan';
    public $timestamps = false;

    protected $fillable = [
        'tipo',
        'activo',
        'id_usuario',
        'observaciones'
    ];

    public function Padron()
    {
        return $this->hasMany(AfiliadoPadronEntity::class, 'id_padron');
    }
}
