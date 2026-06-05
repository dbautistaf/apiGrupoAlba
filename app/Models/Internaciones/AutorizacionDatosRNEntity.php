<?php

namespace App\Models\Internaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutorizacionDatosRNEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_prestaciones_medicas_rn';
    protected $primaryKey = 'cod_prestacion_rn';
    public $timestamps = false;
    protected $appends = ['numero_tramite', 'cod_prestacion'];

    protected $fillable = [
        'fecha_registra',
        'observaciones',
        'fecha_impresion',
        'vigente',
        'monto_pagar',
        'usuario_registra',
        'usuario_imprime',
        'cod_prestador',
        'cod_profesional',
        'cod_recien_nacido',
        'estado_impresion',
        'cod_tipo_estado',
        'diagnostico',
        'id_diagnostico',
        'domicilio_prestador',
        'domicilio_profesional',
        'observacion_interna',
        'fecha_modifica'
    ];

    public function detalle_prestacion()
    {
        return $this->hasMany(AutorizacionDetalleRNEntity::class, 'cod_prestacion_rn', 'cod_prestacion_rn');
    }

    public function getNumeroTramiteAttribute()
    {
        return $this->cod_prestacion_rn;
    }

    public function getCodPrestacionAttribute()
    {
        return $this->cod_prestacion_rn;
    }
}
