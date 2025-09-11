<?php

namespace App\Models\facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturacionTipoImputacionContableEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_facturacion_tipo_imputacion_contable';
    protected $primaryKey = 'id_tipo_imputacion';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
