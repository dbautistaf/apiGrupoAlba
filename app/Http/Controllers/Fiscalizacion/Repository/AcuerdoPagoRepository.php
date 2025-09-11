<?php

namespace App\Http\Controllers\Fiscalizacion\Repository;

use App\Models\Fiscalizacion\AcuerdoPagoEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcuerdoPagoRepository
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
        return AcuerdoPagoEntity::create([
            'id_expediente' => $params->id_expediente,
            'fecha_alta' => $this->fechaActual,
            'usuario_alta' => $this->user->cod_usuario,
            'estado' => $params->estado,
            'monto_total' => $params->monto_total,
            'observaciones' => $params->observaciones,
        ]);
    }

    public function findByUpdate($params)
    {
        $acuerdo = AcuerdoPagoEntity::find($params->id_acuerdo_pago);
        $acuerdo->estado = $params->estado;
        $acuerdo->monto_total = $params->monto_total;
        $acuerdo->observaciones = $params->observaciones;
        $acuerdo->usuario_modifica = $this->user->cod_usuario;
        $acuerdo->fecha_modifica = $this->fechaActual;
        $acuerdo->update();
        return $acuerdo;
    }

    public function findByExpediente($id_expediente)
    {
        return AcuerdoPagoEntity::where('id_expediente', $id_expediente)->get();
    }
}
