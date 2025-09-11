<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GestoriaModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_gestoria';
    protected $primaryKey = 'id_gestoria';
    public $timestamps = false;

    protected $fillable = [
        'nombre_gestoria',
        'activo'
    ];
}
