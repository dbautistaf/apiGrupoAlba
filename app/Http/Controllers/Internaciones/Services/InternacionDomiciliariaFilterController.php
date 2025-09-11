<?php

namespace App\Http\Controllers\Internaciones\Services;

use App\Http\Controllers\Internaciones\Repository\IternacionDomiciliariaFilterRepository;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class InternacionDomiciliariaFilterController extends Controller
{
    public function getListarServicios(Request $request, IternacionDomiciliariaFilterRepository $repo)
    {
        $data = [];

        if (!is_null($request->search)) {
            $data = $repo->findByListServiciosTipoTop($request->search, 20);
        } else {
            $data = $repo->findByListServiciosTop(100);
        }

        return response()->json($data);
    }

    public function getListarSolicitudes(Request $request, IternacionDomiciliariaFilterRepository $repo)
    {
        $data = [];

        $data = $repo->finByListSolicitudesAndLimit($request->desde, $request->hasta, 100);

        return response()->json($data);
    }

    public function getBuscarSolicitudId(Request $request, IternacionDomiciliariaFilterRepository $repo)
    {
        return response()->json($repo->findBySolicitudId($request->id));
    }

    public function getListarHistorialCosto(Request $request, IternacionDomiciliariaFilterRepository $repo)
    {
        return response()->json($repo->findByListHistorialCostoBetween($request->id));
    }
}
