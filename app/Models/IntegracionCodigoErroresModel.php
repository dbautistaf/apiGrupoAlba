<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegracionCodigoErroresModel extends Model
{
    use HasFactory;
    protected $table = 'tb_codigo_errores_discapacidad';
    protected $primaryKey = 'cod_error';
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'cod_error',
        'campo',
        'descripcion',
        'accion',
    ];
}
