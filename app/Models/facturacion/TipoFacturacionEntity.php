<?php

namespace App\Models\facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoFacturacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_facturacion_tipo';
    protected $primaryKey = 'id_tipo_factura';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
