<?php

namespace App\Http\Controllers\prestadores;

use App\Models\prestadores\PrestadorTipoPagoEntity;
use App\Models\prestadores\TipoEfectorEntity;
use App\Models\prestadores\TipoRegimenGananciaEntity;
use Illuminate\Routing\Controller;

class TipoEfectorController extends Controller
{
    public function getTipoRegimenGanancia()
    {
        $data = [];

        $data = TipoRegimenGananciaEntity::orderBy('regimen')
            ->get();

        return response()->json($data, 200);
    }

    public function getTipoEfector()
    {
        $data = [];

        $data = TipoEfectorEntity::orderBy('tipo')
            ->get();

        return response()->json($data, 200);
    }

    public function getTipoPrestadorPago()
    {
        $data = PrestadorTipoPagoEntity::orderBy('tipo_pago')->get();
        return response()->json($data, 200);
    }
}
