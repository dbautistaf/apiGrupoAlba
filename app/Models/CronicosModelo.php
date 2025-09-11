<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CronicosModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_cronicos';
    protected $primaryKey = 'id_cronico';
    public $timestamps = false;

    protected $fillable = [
        'id_patologia',
        'observaciones',
        'fecha_alta',
        'fecha_baja',
        'fecha_carga',
        'id_usuario',
        'id_padron'
    ];
}
