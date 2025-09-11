<?php

namespace App\Models\pratricaMatriz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticaPadreEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_practica_padres';
    protected $primaryKey = 'id_padre';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente',
        'clave',
    ];
}
