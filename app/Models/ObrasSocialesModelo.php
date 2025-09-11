<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObrasSocialesModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_obras_sociales';
    protected $primaryKey = 'id_obra';
    public $timestamps = false;

    protected $fillable = [
        'rnos',
        'denominacion',
        'sigla',
        'id_gerenciadora'
    ];
}
