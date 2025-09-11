<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConveniosAltaCategoriaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_alta_categoria';
    protected $primaryKey = 'id_alta_categoria';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];

    protected $hidden = [
        'pivot',
    ];
}
