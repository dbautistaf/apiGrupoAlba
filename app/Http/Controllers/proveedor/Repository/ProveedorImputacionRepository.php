<?php

namespace App\Http\Controllers\proveedor\Repository;

use App\Models\proveedor\ImputacionProveedorEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProveedorImputacionRepository
{
    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findByAgregarImputaciones($params, $cod_proveedor)
    {
        return ImputacionProveedorEntity::create([
            'id_tipo_imputacion_contable' => $params['id_tipo_imputacion_contable'],
            'cod_proveedor' => $cod_proveedor,
            'fecha_carga' => $this->fechaActual,
            'cod_usuario_carga' => $this->user->cod_usuario,
            'clasificacion' => $params['clasificacion']
        ]);
    }

    public function findByUpdateImputaciones($params, $cod_proveedor)
    {
        $imputa = ImputacionProveedorEntity::find($params['id_imputacion_proveedor']);
        $imputa->fecha_modifica = $this->fechaActual;
        $imputa->cod_usuario_modifica = $this->user->cod_usuario;
        $imputa->clasificacion = $params['clasificacion'];
        $imputa->id_tipo_imputacion_contable = $params['id_tipo_imputacion_contable'];
        $imputa->update();
        return $imputa;
    }

    public function findByAnularImputaciones($params)
    {
        $imputa = ImputacionProveedorEntity::find($params->id_imputacion_proveedor);
        $imputa->vigente = $params->vigente;
        $imputa->update();
        return $imputa;
    }

    public function findByListarImputaciones($proveedor)
    {
        return ImputacionProveedorEntity::with(['imputacion'])
            ->where('vigente', '1')
            ->where('cod_proveedor', $proveedor)
            ->get();
    }
}
