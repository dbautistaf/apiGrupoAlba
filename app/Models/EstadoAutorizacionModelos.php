<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoAutorizacionModelos extends Model
{
    use HasFactory;
    protected $table = 'tb_estado_autorizacion';
    protected $primaryKey = 'id_estado_autorizacion';
    public $timestamps = false;

    protected $fillable = [
        'estado_autorizacion'
    ];
}
