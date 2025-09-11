<?php

namespace App\Http\Controllers\convenios;

use App\Models\convenios\ConvenioTipoSectoresEntity;
use Illuminate\Routing\Controller;

class ConvenioNegociacionTipoSectoresController extends Controller
{
    public function getListarTipoSectores()
    {
        return response(ConvenioTipoSectoresEntity::where('activo', 1)->get(), 200);
    }
}
