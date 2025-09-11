<?php

namespace App\Models\facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturacionAuditarEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_facturacion_auditar';
    protected $primaryKey = 'id_auditar';
    public $timestamps = false;

    protected $fillable = [
        'id_detalle',
        'estado_autoriza',
        'observacion_rechazo',
        'cod_usuario',
        'fecha_audita',
        'monto_debito'
    ];
}
