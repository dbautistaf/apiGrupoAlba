<?php

namespace App\Models;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tb_usuarios';
    protected $primaryKey = 'cod_usuario';
    public $timestamps = false;

    protected $fillable = [
        'nombre_apellidos',
        'documento',
        'telefono',
        'direccion',
        'fecha_alta',
        'fecha_baja',
        'estado_cuenta',
        'fecha_cambio_clave',
        'email',
        'codigo_verificacion',
        'password',
        'cod_perfil',
        'fecha_registra'
    ];

    public function perfil()
    {
        return $this->hasOne(PerfilModelo::class, 'cod_perfil', 'cod_perfil');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'codigo_verificacion',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        $accesos = app(\App\Http\Controllers\AuthController::class)->srvMenuAcceso($this->cod_perfil);
        $query = AfiliadoPadronEntity::where('dni', $this->documento)->first();
       
        return [
            'username' => $this->nombre_apellidos,
            'email' => $this->email,
            'perfil' => $this->perfil->nombre_perfil,
            'id_perfil'=>$this->perfil->cod_perfil,
            'acceso' => $accesos,
            'locatario' => $query->id_locatario ?? null,
        ];
    }
}
