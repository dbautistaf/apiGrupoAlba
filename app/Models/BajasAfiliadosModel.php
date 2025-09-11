<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BajasAfiliadosModel extends Model
{
    use HasFactory;
    protected $table = 'tb_afiliados_eliminados';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'cuil_tit',
        'cuil_benef',
        'dni',
        'nombres',
        'apellidos',
        'fech_nac',
        'fech_eliminado',
        'id_usuario'
    ];
}
