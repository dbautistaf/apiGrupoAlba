<?php

namespace App\Models\Internaciones;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternacionesNotasEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_internaciones_notas';
    protected $primaryKey = 'cod_notas';
    public $timestamps = false;

    protected $fillable = [
        'dni_afiliado',
        'cod_usuario',
        'fecha_registra',
        'descripcion'
    ];

    public function usuario()
    {
        return $this->hasOne(User::class, 'cod_usuario', 'cod_usuario');
    }
}
