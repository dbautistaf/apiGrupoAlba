<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoporteProductosModelo extends Model
{
    use HasFactory;
    protected $connection = 'mysql_soporte';
    protected $table = 'tb_productos';
    protected $primaryKey = 'id_tipo_producto';
    public $timestamps = false;

    protected $fillable = [
        'tipo_producto'
    ];
}
