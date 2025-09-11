<?php

namespace App\Models\facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturacionTipoLetraEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_facturacion_tipo_letra';
    protected $primaryKey = 'tipo';
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'tipo',
        'descripcion',
        'vigente'
    ];
}
