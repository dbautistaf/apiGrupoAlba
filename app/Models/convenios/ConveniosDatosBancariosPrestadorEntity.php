<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConveniosDatosBancariosPrestadorEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_datos_bancarios_prestador';
    protected $primaryKey = 'id_datos_bancarios_prestador';
    public $timestamps = false;

    protected $fillable = [
        'cbu',
        'descripcion',
        'cod_convenio_prestador',
        'id_tipo_cbu',
        'vigente'
    ];
}
