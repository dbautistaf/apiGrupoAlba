<?php

namespace App\Models\pratricaMatriz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticaTipoGalenoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_practica_tipo_galeno';
    protected $primaryKey = 'id_tipo_galeno';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
