<?php

namespace App\Models\Internaciones;

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
}
