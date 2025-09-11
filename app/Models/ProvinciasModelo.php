<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvinciasModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_provincias';
    protected $primaryKey = 'id_provincia';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'activo'
    ];
}
