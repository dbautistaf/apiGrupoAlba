<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCarpetaModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_carpeta';
    protected $primaryKey = 'id_tipo_carpeta';
    public $timestamps = false;

    protected $fillable = [
        'tipo_carpeta'
    ];
}
