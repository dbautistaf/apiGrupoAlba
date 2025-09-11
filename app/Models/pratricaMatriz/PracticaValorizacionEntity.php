<?php

namespace App\Models\pratricaMatriz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticaValorizacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_practica_valorizacion';
    protected $primaryKey = 'id_practica_valorizacion';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
