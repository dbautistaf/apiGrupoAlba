<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostoPracticaLaboratorioEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_practicas_valores_pago';
    protected $primaryKey = 'cod_valor_pago';
    public $timestamps = false;

    protected $fillable = [
        'afiliado_discapacidad',
        'afiliado_estemb',
        'afiliado_procede',
        'costo_practica',
        'practica_valor',
        'descripcion',
        'fecha_registra'
    ];
}
