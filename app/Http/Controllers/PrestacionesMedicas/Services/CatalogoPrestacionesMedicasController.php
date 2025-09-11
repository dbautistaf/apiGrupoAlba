<?php

namespace App\Http\Controllers\PrestacionesMedicas\Services;

use App\Http\Controllers\PrestacionesMedicas\Repository\CatalogoPrestacionesMedicasRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CatalogoPrestacionesMedicasController extends Controller
{


    public function getListarTipoTramites(CatalogoPrestacionesMedicasRepository $repo)
    {
        return response()->json($repo->findByListTipoTramite());
    }

    public function getCrearTipoTramite(CatalogoPrestacionesMedicasRepository $repo, Request $request)
    {
        return response()->json($repo->findByCreateTipoTramite($request));
    }

    public function getListarTipoPrioridad(CatalogoPrestacionesMedicasRepository $repo)
    {
        return response()->json($repo->findByListTipoPrioridad());
    }

    public function getCrearTipoPrioridad(CatalogoPrestacionesMedicasRepository $repo, Request $request)
    {
        return response()->json($repo->findByCreateTipoPrioridad($request));
    }
}
