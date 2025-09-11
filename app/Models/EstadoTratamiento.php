<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoTratamiento extends Model
{
    use HasFactory;
    protected $table = 'tb_estado_tratamiento';
    protected $primaryKey = 'id_estado_tratamiento';
    public $timestamps = false;

    protected $fillable = [
        'detalle_tratamiento',
        'estado'
    ];
}
