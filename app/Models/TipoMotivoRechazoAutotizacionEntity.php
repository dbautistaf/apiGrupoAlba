<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoMotivoRechazoAutotizacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_rechazo_prestacion';
    protected $primaryKey = 'cod_tipo_rechazo';
    public $timestamps = false;

    protected $fillable = [
        'descripcion_corta',
        'descripcion_larga',
        'vigente'
    ];
}
