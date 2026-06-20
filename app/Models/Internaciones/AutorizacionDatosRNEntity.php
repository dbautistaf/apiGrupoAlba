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
        'fecha_modifica',
        'id_locatorio',
        'cod_sindicato',
        'id_tipo_tramite'
    ];

    public function detalle_prestacion()
    {
        return $this->hasMany(AutorizacionDetalleRNEntity::class, 'cod_prestacion_rn', 'cod_prestacion_rn');
    }

    public function detalle()
    {
        return $this->hasMany(AutorizacionDetalleRNEntity::class, 'cod_prestacion_rn', 'cod_prestacion_rn');
    }

    public function recien_nacido()
    {
        return $this->belongsTo(RecienNacidoEntity::class, 'cod_recien_nacido', 'cod_recien_nacido');
    }

    public function estadoPrestacion()
    {
        return $this->hasOne(\App\Models\PrestacionesMedicas\TipoEstadoPrestacionEntity::class, 'cod_tipo_estado', 'cod_tipo_estado');
    }

    public function usuario()
    {
        return $this->hasOne(\App\Models\User::class, 'cod_usuario', 'usuario_registra');
    }

    public function obraSocial()
    {
        return $this->belongsTo(\App\Models\LocatorioModelos::class, 'id_locatorio', 'id_locatorio');
    }

    public function tipoTramite()
    {
        return $this->belongsTo(\App\Models\PrestacionesMedicas\TipoTramiteAutorizacionesEntity::class, 'id_tipo_tramite', 'id_tipo_tramite');
    }

    public function prestador()
    {
        return $this->hasOne(\App\Models\prestadores\PrestadorEntity::class, 'cod_prestador', 'cod_prestador');
    }

    public function profesional()
    {
        return $this->hasOne(\App\Models\prestadores\PrestadorMedicosEntity::class, 'cod_profesional', 'cod_profesional');
    }

    public function getNumeroTramiteAttribute()
    {
        return $this->cod_prestacion_rn;
    }

    public function getCodPrestacionAttribute()
    {
        return $this->cod_prestacion_rn;
    }

    public function getAfiliadoAttribute()
    {
        return $this->recien_nacido?->internacion?->afiliado;
    }

    public function getAutorizacionRnAttribute()
    {
        return $this;
    }

    public function getDatosTramiteAttribute()
    {
        return [
            'tramite' => [
                'descripcion' => 'AUTORIZACIÓN RECIÉN NACIDO'
            ],
            'prioridad' => [
                'descripcion' => 'NORMAL'
            ]
        ];
    }
}
