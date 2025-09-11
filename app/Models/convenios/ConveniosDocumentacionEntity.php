<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConveniosDocumentacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_documentacion';
    protected $primaryKey = 'cod_documentacion';
    public $timestamps = false;

    protected $fillable = [
        'nombre_archivo',
        'fecha_crea',
        'cod_usuario',
        'observaciones',
        'cod_convenio'
    ];
}
