<?php

namespace App\Models\articulos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticuloFamiliaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_articulos_familia';
    protected $primaryKey = 'id_familia';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
