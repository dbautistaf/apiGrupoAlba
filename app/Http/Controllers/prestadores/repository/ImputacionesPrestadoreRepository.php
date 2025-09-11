<?php

namespace App\Http\Controllers\Prestadores\repository;

use App\Models\prestadores\PrestadorImputacionesContablesEntity;
use App\Models\prestadores\TipoInputacionesContablesEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ImputacionesPrestadoreRepository
{
    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findByListImputaciones($prestador)
    {
        return PrestadorImputacionesContablesEntity::with(['imputacion', 'prestador'])
            ->where('vigente', '1')
            ->where('cod_prestador', $prestador)
            ->get();
    }

    public function findByAgregarImputaciones($params, $cod_prestador)
    {
        return PrestadorImputacionesContablesEntity::create([
            'id_tipo_imputacion_contable' => $params['id_tipo_imputacion_contable'],
            'cod_prestador' => $cod_prestador,
            'fecha_carga' => $this->fechaActual,
            'cod_usuario_carga' => $this->user->cod_usuario,
            'clasificacion' => $params['clasificacion']
        ]);
    }

    public function findByUpdateImputaciones($params, $cod_prestador)
    {
        $imputa = PrestadorImputacionesContablesEntity::find($params['id_imputacion_prestador']);
        $imputa->fecha_modifica = $this->fechaActual;
        $imputa->cod_usuario_modifica = $this->user->cod_usuario;
        $imputa->clasificacion = $params['clasificacion'];
        $imputa->id_tipo_imputacion_contable = $params['id_tipo_imputacion_contable'];
        $imputa->update();
        return $imputa;
    }

    public function findByAnularImputaciones($params)
    {
        $imputa = PrestadorImputacionesContablesEntity::find($params->id_imputacion_prestador);
        $imputa->vigente = $params->vigente;
        $imputa->update();
        return $imputa;
    }

    public function findByCrearTipoImputacion($params)
    {
        return TipoInputacionesContablesEntity::create([
            'imputacion' => $params->imputacion,
            'codigo' => $params->codigo,
            'vigente' => $params->vigente,
            'cod_usuario' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual,
            'imputable' => $params->imputable,
            'tipo' => $params->tipo,
            'imputacion_relacionada' => $params->imputacion_relacionada,
            'sector_relacionado' => $params->sector_relacionado,
        ]);
    }

    public function findByUpdateTipoImputacion($params)
    {
        $tipo =  TipoInputacionesContablesEntity::find($params->id_tipo_imputacion_contable);
        $tipo->imputacion = $params->imputacion;
        $tipo->codigo = $params->codigo;
        $tipo->vigente = $params->vigente;
        $tipo->cod_usuario_modifica  = $this->user->cod_usuario;
        $tipo->fecha_modifica = $this->fechaActual;
        $tipo->imputable = $params->imputable;
        $tipo->tipo = $params->tipo;
        $tipo->imputacion_relacionada = $params->imputacion_relacionada;
        $tipo->sector_relacionado = $params->sector_relacionado;
        $tipo->update();
        return $tipo;
    }

    public function findByDeleteTipoImputacion($params)
    {
        $tipo =  TipoInputacionesContablesEntity::find($params->id_tipo_imputacion_contable);
        $tipo->vigente = $params->vigente;
        $tipo->update();
        return $tipo;
    }

    public function findByExistsTipoImputacion($codigo): bool
    {
        return TipoInputacionesContablesEntity::where('codigo', $codigo)->exists();
    }
}
