<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConvenioTipoRespuestaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_tipo_respuesta';
    protected $primaryKey = 'id_tipo_respuesta';
    public $timestamps = false;

    protected $fillable = [
        'tipo_respuesta',
        'activo'
    ];
}
