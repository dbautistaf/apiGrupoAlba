<?php

namespace App\Http\Controllers\Internaciones\Services;

use App\Http\Controllers\Internaciones\Repository\AuxiliaresRepository;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class CatalogoInternacionController extends Controller
{

    public function getListarTipoPrestacion(AuxiliaresRepository $repo)
    {
        return response()->json(
            $repo->findByTipoPrestacion(),
            200
        );
    }

    public function getListarTipoInternacion(AuxiliaresRepository $repo)
    {
        return response()->json(
            $repo->findByTipoInternacion(),
            200
        );
    }

    public function getListarTipoHabitacionInternacion(AuxiliaresRepository $repo)
    {
        return response()->json(
            $repo->findByTipoHabitacion(),
            200
        );
    }

    public function getListarTipoCategoriaInternacion(AuxiliaresRepository $repo)
    {
        return response()->json(
            $repo->findByTipoCategoriaInternacion(),
            200
        );
    }

    public function getListarTipoFacturacionInternacion(AuxiliaresRepository $repo)
    {
        return response()->json(
            $repo->findByTipoPacturacionInternacion(),
            200
        );
    }

    public function getListarTipoEgresoInternacion(AuxiliaresRepository $repo)
    {
        return response()->json(
            $repo->findByTipoEgresoInternacion(),
            200
        );
    }

    public function getListarTipoDiagnosticoInternacion(AuxiliaresRepository $repo, Request $request)
    {
        $data = [];
        if (!empty($request->search)) {
            $data = $repo->findByTipoDiagnosticoInternacion($request->search);
        } else {
            $data = $repo->findByTipoDiagnosticoInternacionLimit(50);
        }
        return response()->json($data, 200);
    }

    public function getListarTipoDiagnosticoInternacionId(AuxiliaresRepository $repo, Request $request)
    {
        return response()->json(
            $repo->findByTipoDiagnosticoInternacionId($request->id),
            200
        );
    }

    public function getCrearDiagnostico(AuxiliaresRepository $repo, Request $request)
    {
        return response()->json(
            $repo->findByCreateDiagnostico($request),
            200
        );
    }
}
