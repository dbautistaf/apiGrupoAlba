<?php

namespace App\Models\liquidaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiquidacionDocumentosAdjuntosEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_liquidaciones_documentacion_adjunta';
    protected $primaryKey = 'cod_documentacion';
    public $timestamps = false;

    protected $fillable = [
        'archivo_auditoria',
        'archivo_respaldo',
        'detalle_prestacion',
        'id_factura',
        'observaciones',
        'fecha_carga',
        'cod_usuario'
    ];
}
