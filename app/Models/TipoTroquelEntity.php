<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoTroquelEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tipo_troquel';
    protected $primaryKey = 'cod_tipo_troquel';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
