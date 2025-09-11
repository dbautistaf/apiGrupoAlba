<?php

namespace App\Models\articulos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticuloRubrosEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_articulos_rubro';
    protected $primaryKey = 'id_rubro';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
