<?php

namespace App\Http\Controllers\convenios;

use App\Models\convenios\ConvenioTipoRespuestaEntity;
use Illuminate\Routing\Controller;

class ConvenioNegociacionTipoRespuestaController extends Controller
{
    public function getListarTipoRespuesta()
    {
        return response(ConvenioTipoRespuestaEntity::where('activo', 1)->get(), 200);
    }
}
