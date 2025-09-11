<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileRecetas extends Model
{
    use HasFactory;
    protected $table = 'tb_recetas_file';
    protected $primaryKey = 'id_file';
    public $timestamps = false;

    protected $fillable = [
        'nombre_file',
        'id_receta',
        'fecha_proceso',
    ];
}
