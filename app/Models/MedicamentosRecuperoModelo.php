<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicamentosRecuperoModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_medicamentos_recupero';
    protected $primaryKey = 'id_medicamento_recupero';
    public $timestamps = false;

    protected $fillable = [
        'atc',
        'generico',
        'dosis',
        'presentacion',
        'reintegro_por_unidad'
    ];
}
