<?php

namespace App\Models\pratricaMatriz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticaNomencladorEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_practica_nomenclador';
    protected $primaryKey = 'id_nomenclador';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
