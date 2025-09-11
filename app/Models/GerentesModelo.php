<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GerentesModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_gerentes';
    protected $primaryKey = 'id_gerente';
    public $timestamps = false;

    protected $fillable = [
        'nombres_gerente',
        'activo'
    ];
}
