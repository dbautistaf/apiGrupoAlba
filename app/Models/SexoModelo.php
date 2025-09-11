<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SexoModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_sexo';
    protected $primaryKey = 'id_sexo';    
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'sexo'
    ];
}
