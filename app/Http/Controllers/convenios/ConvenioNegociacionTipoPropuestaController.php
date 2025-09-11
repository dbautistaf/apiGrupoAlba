<?php

namespace App\Http\Controllers\convenios;

use App\Models\convenios\ConvenioTipoPropuestaEntity;
use Illuminate\Routing\Controller;

class ConvenioNegociacionTipoPropuestaController extends Controller
{
    public function getListarTipoPropuesta()
    {
        return response(ConvenioTipoPropuestaEntity::where('activo', 1)->get(), 200);
    }
}
