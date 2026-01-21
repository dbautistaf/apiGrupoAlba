<?php

namespace App\Models\Seguridad;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccesoUsuarioEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_acceso_usuarios';
    protected $primaryKey = 'id_acceso';
    public $timestamps = false;
    protected $fillable = [
        'cod_usuario',
        'navegador',
        'plataforma',
        'device',
        'ip',
        'fecha_acceso'
    ];

    public function usuario()
    {
        return $this->hasOne(User::class, 'cod_usuario', 'cod_usuario');
    }
}
