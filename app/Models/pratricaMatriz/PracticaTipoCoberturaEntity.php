<?php

namespace App\Models\pratricaMatriz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticaTipoCoberturaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_practica_tipo_cobertura';
    protected $primaryKey = 'id_tipo';
    public $timestamps = false;

    protected $fillable = [
        'cobertura',
        'vigente'
    ];

    protected $hidden = [
        'pivot',
    ];
}
