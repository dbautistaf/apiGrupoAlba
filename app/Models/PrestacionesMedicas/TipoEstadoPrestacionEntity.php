<?php

namespace  App\Models\PrestacionesMedicas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEstadoPrestacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_estado_prestacion';
    protected $primaryKey = 'cod_tipo_estado';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente',
        'class_name'
    ];
}
