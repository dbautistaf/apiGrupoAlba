<?php

namespace App\Models\facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturacionTipoImputacionSintetizadaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_facturacion_tipo_imputacion_sintetizada';
    protected $primaryKey = 'id_tipo_imputacion_sintetizada';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
