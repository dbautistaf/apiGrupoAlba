<?php

namespace  App\Http\Controllers\Derivacion\Services;

use App\Http\Controllers\Derivacion\Repository\CatalogoDerivacionesRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CatalogoDerivacionController extends Controller
{

    public function getListarTipoSector(CatalogoDerivacionesRepository $repo)
    {
        return response()->json($repo->findByTipoSector());
    }

    public function getListarTipoPaciente(CatalogoDerivacionesRepository $repo)
    {
        return response()->json($repo->findByTipoPaciente());
    }

    public function getListarTipoDerivacion(CatalogoDerivacionesRepository $repo)
    {
        return response()->json($repo->findByTipoDerivacion());
    }

    public function getListarTipoMotivoTraslado(CatalogoDerivacionesRepository $repo)
    {
        return response()->json($repo->findByTipoMotivoTraslado());
    }

    public function getListarTipoMovilTraslado(CatalogoDerivacionesRepository $repo)
    {
        return response()->json($repo->findByTipoMovilTraslado());
    }

    public function getListarTipoEgreso(CatalogoDerivacionesRepository $repo)
    {
        return response()->json($repo->findByTipoEgreso());
    }

    public function getListarTipoRequisitosExtras(CatalogoDerivacionesRepository $repo)
    {
        return response()->json($repo->findByTipoRequisitosExtras());
    }

}
