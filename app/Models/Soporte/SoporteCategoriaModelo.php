<?php

namespace   App\Models\Soporte;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoporteCategoriaModelo extends Model
{
    use HasFactory;
    protected $connection = 'mysql_soporte';
    protected $table = 'tb_categoria';
    protected $primaryKey = 'id_categoria';
    public $timestamps = false;

    protected $fillable = [
        'nombre_categoria',
        'descripcion'
    ];
}
