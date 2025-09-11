<?php

namespace App\Models\facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturacionTipoEfectorEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_facturacion_tipo_efector';
    protected $primaryKey = 'id_tipo_efector';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
