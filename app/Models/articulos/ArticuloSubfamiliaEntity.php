<?php

namespace App\Models\articulos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticuloSubfamiliaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_articulos_subfamilia';
    protected $primaryKey = 'id_subfamilia';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
