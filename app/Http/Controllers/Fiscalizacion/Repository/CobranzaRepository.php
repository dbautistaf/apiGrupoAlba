<?php

namespace App\Http\Controllers\Fiscalizacion\Repository;

use App\Models\Fiscalizacion\CobranzaEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CobranzaRepository
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
        return CobranzaEntity::create([
            'id_empresa' => $params->id_empresa,
            'id_expediente' => $params->id_expediente,
            'id_banco' => $params->id_banco,
            'id_forma_pago' => $params->id_forma_pago,
            'fecha_creacion' => $this->fechaActual->toDateString(),
            'cobro_neto' => $params->cobro_neto,
            'cobro_total' => $params->cobro_total,
            'usuario' => $this->user->nombre_apellidos,
        ]);
    }

    public function findByEmpresa($id_empresa)
    {
        return CobranzaEntity::where('id_empresa', $id_empresa)->get();
    }
}
