<?php

namespace App\Models\prestadores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEfectorEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_prestador_tipo_efector';
    protected $primaryKey = 'id_tipo_efector';
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_efector',
        'tipo'
    ];
}
