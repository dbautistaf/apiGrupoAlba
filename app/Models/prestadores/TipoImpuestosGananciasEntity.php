<?php

namespace App\Models\Prestadores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoImpuestosGananciasEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_impuesto_ganancias';
    protected $primaryKey = 'cod_tipo_impuesto';
    public $timestamps = false;

    protected $fillable = [
        'descripcion_tipo',
        'vigente'
    ];
}
