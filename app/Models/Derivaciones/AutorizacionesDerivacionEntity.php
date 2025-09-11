<?php

namespace  App\Models\Derivaciones;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutorizacionesDerivacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_derivacion_autorizacion';
    protected $primaryKey = 'id_autorizacion';
    public $timestamps = false;

    protected $fillable = [
        'id_derivacion',
        'fecha_autorizacion',
        'cod_usuario',
        'id_tipo_estado',
        'observaciones',
        'motivo_rechazo'
    ];

    public function usuario()
    {
        return $this->hasOne(User::class,  'cod_usuario', 'cod_usuario');
    }
}
