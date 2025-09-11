<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCoberturaRecetarioEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_cobertura_recetario';
    protected $primaryKey = 'cod_cobertura';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
