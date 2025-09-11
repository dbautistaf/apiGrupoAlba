<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAutorizacion extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_autorizacion';
    protected $primaryKey = 'id_tipo_autorizacion';
    public $timestamps = false;

    protected $fillable = [
        'detalle_autorizacion',
        'estado'
    ];
}
