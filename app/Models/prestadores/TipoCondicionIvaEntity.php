<?php

namespace App\Models\prestadores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCondicionIvaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_condicion_iva';
    protected $primaryKey = 'cod_tipo_iva';
    public $timestamps = false;

    protected $fillable = [
        'descripcion_iva',
        'vigente'
    ];
}
