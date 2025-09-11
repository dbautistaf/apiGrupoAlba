<?php

namespace App\Http\Controllers\configuracion;

use App\Models\configuracion\TipoGalenosEntity;
use App\Models\configuracion\TipoPlanGalenosEntity;
use App\Models\pratricaMatriz\PracticaTipoGalenoEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MantenimientoGalenosController extends Controller
{
    public function getListarTipoGalenos()
    {

        return response()->json(PracticaTipoGalenoEntity::where('vigente', 1)
            ->orderBy('descripcion')
            ->get(), 200);
    }

    public function getListarGalenos()
    {
        return response()->json(TipoGalenosEntity::where('vigente', 1)
            ->orderBy('descripcion')
            ->get(), 200);
    }

    public function getListarTipoPlanesGalenos()
    {
        return response()->json(TipoPlanGalenosEntity::where('vigente', 1)
            ->orderBy('descripcion')
            ->get(), 200);
    }
}
