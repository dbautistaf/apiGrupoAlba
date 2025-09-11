<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPracticasLaboratorioEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tipo_practicas';
    protected $primaryKey = 'cod_tipo_practica';
    public $timestamps = false;

    protected $fillable = [
        'codigo_practica',
        'descripcion_practica',
        'practica_nomenclada',
        'practica_especialidad',
        'practica_nomenclado',
        'practica_id_capitulo',
        'practica_id_titulo',
        'practica_escapitulo',
        'practica_valor'
    ];
}
