<?php

namespace App\Models\Diabetes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicamentosDiabetesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_padron_medicamentos_diabetes';
    protected $primaryKey = 'id_medicamento';
    public $timestamps = false;

    protected $fillable = [
        'nombre_medicamento',
        'presentacion',
        'unidad',
        'vigente',
        'cod_usuario',
        'fecha_modifica',
        'cod_usuario_modifica',
        'fecha_registra'
    ];
}
