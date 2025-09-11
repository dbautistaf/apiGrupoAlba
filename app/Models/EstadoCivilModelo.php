<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoCivilModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_estado_civil';
    protected $primaryKey = 'id_estado_civil';
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'estado'
    ];
}
