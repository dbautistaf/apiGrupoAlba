<?php

namespace App\Models\Internaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternacionAutorizacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_internaciones_autorizacion';
    protected $primaryKey = 'id_internacion_autorizacion';
    public $timestamps = false;
    protected $fillable = [
        'cod_internacion',
        'cod_prestacion',
        'fecha_registra',
        'cod_usuario'
    ];
}
