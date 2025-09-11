<?php

namespace   App\Models\Internaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaInternacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_categoria_internacion';
    protected $primaryKey = 'cod_categoria_internacion';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
