<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoSuperModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_estado_super';
    protected $primaryKey = 'id_estado_super';
    public $timestamps = false;

    protected $fillable = [
        'estado'
    ];
}
