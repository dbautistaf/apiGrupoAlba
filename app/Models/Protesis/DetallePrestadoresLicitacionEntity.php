<?php

namespace App\Models\Protesis;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallePrestadoresLicitacionEntity extends Model
{

    use HasFactory;
    protected $table = 'tb_protesis_solicitar_presupuesto';
    protected $primaryKey = 'id_solicitud';
    public $timestamps = false;

    protected $fillable = [
        'id_protesis',
        'cod_prestador',
        'fecha_solicita_presupuesto',
        'cod_usuario',
        'gano_licitacion',
        'fecha_registra_ganador',
        'cod_usuario_registra_ganador',
        'archivo_cotizacion'
    ];
}
