<?php

namespace App\Http\Controllers\Contabilidad\Services;

use App\Http\Controllers\Contabilidad\Repository\CatalogoContabilidadRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CatalogoController extends Controller
{

    public function getTipoPlanCuenta(CatalogoContabilidadRepository $repo)
    {
        return response()->json($repo->findByListarTipoPlanCuenta());
    }

    public function getTipoNiveles(CatalogoContabilidadRepository $repo)
    {
        return response()->json($repo->findByListarNiveles());
    }

    public function getTipoPlanOrganicoCuenta(CatalogoContabilidadRepository $repo)
    {
        return response()->json($repo->findByListarTipoPlanOrganicoCuenta());
    }

    public function getTipoRetencion(CatalogoContabilidadRepository $repo)
    {
        return response()->json($repo->findByListTipoRetencion());
    }
}
