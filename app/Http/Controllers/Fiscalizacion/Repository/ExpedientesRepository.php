<?php

namespace App\Http\Controllers\Fiscalizacion\Repository;

use App\Models\Fiscalizacion\ExpedientesEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ExpedientesRepository
{
    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findByNueva($params)
    {
        return ExpedientesEntity::create([
            'id_empresa' => $params->id_empresa,
            'id_usuario' => $this->user->cod_usuario,
            'numero_expediente' => $params->numero_expediente,
            'fecha_creacion' => $this->fechaActual->toDateString(),
            'tipo_cuenta' => $params->tipo_cuenta,
            'vigente' => $params->vigente ?? 1,
        ]);
    }

    public function findByEmpresa($id_empresa)
    {
        return ExpedientesEntity::where('id_empresa', $id_empresa)->get();
    }
}
