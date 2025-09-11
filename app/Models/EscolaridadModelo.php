<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EscolaridadModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_escolaridad';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nivel_estudio',
        'fecha_presentacion',
        'fecha_vencimiento',
        'id_padron'
    ];
}
