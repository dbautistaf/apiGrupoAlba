<?php

namespace App\Http\Controllers\Internaciones\Services;

use App\Http\Controllers\Internaciones\Repository\AuditarInternacionRepository;
use App\Http\Controllers\Internaciones\Repository\InternacionesRepository;
use App\Http\Controllers\Internaciones\Repository\InternacionFiltrosRepository;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class AuditarInternacionController extends Controller
{

    public function getAuditarInternacion(AuditarInternacionRepository $repoAuditar, InternacionesRepository $repoInternacion, InternacionFiltrosRepository $repoFilterInternacion, Request $request)
    {

        if ($repoFilterInternacion->findByIdExistsAndEstado($request->cod_internacion, 2)) {
            $autorizacion = $repoAuditar->findByAuditar($request);
            $repoInternacion->findByUpdateAndEstado($request->cod_internacion, $autorizacion->cod_tipo_estado);
            return response()->json(['message' => 'Registro auditado correctamente.']);
        } else {
            return response()->json(['message' => 'No se puede auditar un registro si ya fue aduitado anteriormente.'], 409);
        }
    }
}
