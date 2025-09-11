<?php

namespace App\Models\prestadores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPrestadorEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_prestador';
    protected $primaryKey = 'cod_tipo_prestador';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
