<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAreaModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_area';
    protected $primaryKey = 'cod_tipo_area';
    public $timestamps = false;

    protected $fillable = [
        'tipo_area'
    ];
}
