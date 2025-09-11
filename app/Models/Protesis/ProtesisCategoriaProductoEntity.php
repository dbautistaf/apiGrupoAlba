<?php

namespace App\Models\Protesis;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class   ProtesisCategoriaProductoEntity extends Model
{

    use HasFactory;
    protected $table = 'tb_protesis_categoria_producto';
    protected $primaryKey = 'id_categoria';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
