<?php

namespace   App\Models\PrestacionesMedicas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class   TipoTramiteAutorizacionesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_prestacion_medica_tipo_tramite';
    protected $primaryKey = 'id_tipo_tramite';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'estado'
    ];
}
