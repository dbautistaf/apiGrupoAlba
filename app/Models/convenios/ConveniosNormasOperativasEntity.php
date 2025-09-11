<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConveniosNormasOperativasEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_normas_operativas';
    protected $primaryKey = 'cod_norma_operativa';
    public $timestamps = false;

    protected $fillable = [
        'nombre_archivo',
        'observacion',
        'cod_usuario',
        'fecha_crea',
        'cod_convenio'
    ];
}
