<?php

namespace App\Models\Afip;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DdjjEmpresasModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_ddjj_empresas';
    protected $primaryKey = 'id_ddjj_empresa';
    public $timestamps = false;

    protected $fillable = [
        'cuit',
        'nombre_empresa',
        'calle',
        'numero',
        'piso',
        'localidad',
        'cod_prov',
        'cp',
        'cod_os',
        'periodo',
        'fecha_proceso',
        'id_usuario',
    ];
}
