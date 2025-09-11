<?php

namespace App\Http\Controllers\Fiscalizacion\Repository;

use App\Models\Fiscalizacion\IntimacionEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class IntimacionRepository
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
        return IntimacionEntity::create([
            'id_empresa' => $params->id_empresa,
            'id_movimiento' => $params->id_movimiento,
            'numero_registro' => $params->numero_registro,
            'fecha_registra' => $this->fechaActual->toDateString(),
            'fecha_vencimiento_gestion' => $params->fecha_vencimiento_gestion,
            'nombre_usuario' => $this->user->nombre_apellidos,
            'estado_tramite' => $params->estado_tramite,
        ]);
    }

    public function findByEmpresa($id_empresa)
    {
        return IntimacionEntity::where('id_empresa', $id_empresa)->get();
    }
}
