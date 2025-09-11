<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NacionalidadModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_nacionalidad';
    protected $primaryKey = 'id_nacionalidad';
    public $timestamps = false;

    protected $fillable = [
        'CodNac',
        'Nombre'
    ];
}
