<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoProcesoModel extends Model
{
    use HasFactory;
    protected $table = 'td_estado_proceso';
    protected $primaryKey = 'id_estado_proceso';
    public $timestamps = false;

    protected $fillable = [
        'estado_proceso'
    ];
}
