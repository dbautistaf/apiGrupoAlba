<?php

namespace App\Models\facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturacionTipoIvaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_facturacion_tipo_iva';
    protected $primaryKey = 'id_tipo_iva';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'valor_iva',
        'vigente'
    ];
}
