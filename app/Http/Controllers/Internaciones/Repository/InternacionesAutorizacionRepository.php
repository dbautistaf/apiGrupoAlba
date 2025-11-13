<?php

namespace App\Http\Controllers\Internaciones\Repository;

use App\Models\Internaciones\InternacionAutorizacionEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InternacionesAutorizacionRepository
{
    //
    private $user;
    private $fechaActual;
    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findBySave($detalle, $cod_internacion)
    {

        if (!empty($detalle) && !empty($cod_internacion)) {
            foreach ($detalle as $key) {
                InternacionAutorizacionEntity::create([
                    'cod_internacion' => $cod_internacion,
                    'cod_prestacion' => $key,
                    'fecha_registra' => $this->fechaActual,
                    'cod_usuario' => $this->user->cod_usuario,
                ]);
            }
        }
    }

    public function findByUpdate($detalle, $cod_internacion)
    {
        if (!empty($detalle) && !empty($cod_internacion)) {
            $internacion = InternacionAutorizacionEntity::find($cod_internacion);
            if ($internacion != null) {
                $internacion->delete();
            }

            foreach ($detalle as $key) {
                InternacionAutorizacionEntity::create([
                    'cod_internacion' => $cod_internacion,
                    'cod_prestacion' => $key,
                    'fecha_registra' => $this->fechaActual,
                    'cod_usuario' => $this->user->cod_usuario,
                ]);
            }
        }
    }
}
