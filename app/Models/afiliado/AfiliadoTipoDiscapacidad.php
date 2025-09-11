<?php

namespace App\Models\afiliado;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfiliadoTipoDiscapacidad extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_discapacidad';
    protected $primaryKey = 'id_tipo_discapacidad';
    public $timestamps = false;

    protected $fillable = [
        'tipo_discapacidad'
    ];
}
