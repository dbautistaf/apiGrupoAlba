<?php

namespace App\Models\Discapacidad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscapacidadDrEnvioEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_discapacidad_dr_envio';
    protected $primaryKey = 'clave_rendicion';
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'clave_rendicion',
        'rnos',
        'tipo_archivo',
        'periodo_presentacion',
        'periodo_prestacion',
        'cuil',
        'cod_practica',
        'importe_subsidiado',
        'importe_solicitado',
        'cuit_prestador',
        'tipo_comprobante',
        'numero_comprobante',
        'punto_venta',
        'numero_envio_afip',
        'estado_validado_tesoreria'
    ];
}
