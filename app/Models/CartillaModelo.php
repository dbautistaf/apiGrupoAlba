<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartillaModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_cartilla';
    protected $primaryKey = 'id_cartilla';
    public $timestamps = false;

    protected $fillable = [
        'prestador',
        'tipo',
        'calle',
        'piso',
        'telefono_turnos',
        'whatsapp',
        'localidad',
        'zona',
        'especialidades',
        'activo'
    ];
}
