<?php

namespace App\Models\articulos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticuloUnidadMedidaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_articulos_unidad_medida';
    protected $primaryKey = 'id_unidad_medida';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
