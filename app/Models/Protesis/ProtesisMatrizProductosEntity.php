<?php

namespace App\Models\Protesis;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProtesisMatrizProductosEntity extends Model
{

    use HasFactory;
    protected $table = 'tb_protesis_matriz_productos';
    protected $primaryKey = 'id_producto';
    public $timestamps = false;

    protected $fillable = [
        'id_categoria',
        'descripcion_producto',
        'presentacion',
        'monodroga',
        'laboratorio',
        'fecha_crea',
        'cod_usuario',
        'fecha_actualiza',
        'material',
        'observaciones',
        'vigente'
    ];


    public function categoria()
    {
        return $this->hasOne(ProtesisCategoriaProductoEntity::class, 'id_categoria', 'id_categoria');
    }
}
