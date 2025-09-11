<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SituacionRevistaModelo extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    protected $table = 'tb_situacion_revista';
    protected $primaryKey = 'id_situacion_de_revista';
    public $timestamps = false;

    protected $fillable = [
        'situacion'
    ];
}
