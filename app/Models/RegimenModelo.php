<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegimenModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_regimen';
    protected $primaryKey = 'id_regimen';
    public $timestamps = false;

    protected $fillable = [
        'nombre_regimen',
        'activo'
    ];
}
