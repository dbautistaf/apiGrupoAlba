<?php

namespace App\Models\prestadores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoRegimenGananciaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_prestador_regimen_ganancia';
    protected $primaryKey = 'id_regimen';
    public $timestamps = false;

    protected $fillable = [
        'regimen'
    ];
}
