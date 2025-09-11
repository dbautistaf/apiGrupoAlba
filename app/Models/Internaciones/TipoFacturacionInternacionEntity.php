<?php

namespace  App\Models\Internaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoFacturacionInternacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_facturacion';
    protected $primaryKey = 'cod_tipo_facturacion';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
