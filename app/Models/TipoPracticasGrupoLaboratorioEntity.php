<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPracticasGrupoLaboratorioEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_practicas_grupo';
    protected $primaryKey = 'cod_practica_grupo';
    public $timestamps = false;

    protected $fillable = [
        'descripcion_corta',
        'descripcion_larga',
        'vigente'
    ];
}
