<?php

namespace App\Http\Controllers\PrestacionesMedicas\Services;

use App\Http\Controllers\PrestacionesMedicas\Repository\SolicitudLentesFilterRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SolicitudLentesFilterController extends Controller
{


    public function getListarData(SolicitudLentesFilterRepository $repo, Request $request)
    {
        $data = [];

        if (!is_null($request->search)) {
            if (is_numeric($request->search)) {
                $data = $repo->findByListSolicitudesFechaAndAfiliado($request->desde, $request->hasta, $request->search);
            } else {
                $data = $repo->findByListSolicitudesFechaAndAfiliadoNombres($request->desde, $request->hasta, $request->search);
            }
        } else {
            $data = $repo->findByListSolicitudes($request->desde, $request->hasta);
        }
        return response()->json($data);
    }

    public function getSolicitudId(SolicitudLentesFilterRepository $repo, Request $request)
    {
        return response()->json($repo->findBySolicitudId($request->id));
    }
}
