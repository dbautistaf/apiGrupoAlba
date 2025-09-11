<?php

namespace App\Models\pratricaMatriz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticaTipoCoseguroEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_practica_tipo_coseguro';
    protected $primaryKey = 'id_tipo_coseguro';
    public $timestamps = false;

    protected $fillable = [
        'coseguro',
        'vigente'
    ];

}
