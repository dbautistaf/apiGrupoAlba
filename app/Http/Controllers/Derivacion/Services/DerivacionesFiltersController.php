<?php

namespace App\Http\Controllers\Derivacion\Services;

use App\Http\Controllers\Derivacion\Repository\DerivacionFiltersRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DerivacionesFiltersController extends Controller
{

    public function getListar(DerivacionFiltersRepository $repo, Request $request)
    {
        $data = [];
        if (!is_null($request->afiliado)) {
            if (is_numeric($request->afiliado)) {
                $data = $repo->findByListBetweenAndDni($request->desde, $request->hasta, $request->afiliado, 10);
            } else {
                $data = $repo->findByListBetweenAndAfiliado($request->desde, $request->hasta, $request->afiliado, 10);
            }
        } else {
            $data = $repo->findByListBetween($request->desde, $request->hasta, 100);
        }


        return response()->json($data, 200);
    }

    public function getDerivacionesAfiliadoDni(DerivacionFiltersRepository $repo, Request $request)
    {
        $data = $repo->findByListDni($request->dni);

        return response()->json($data, 200);
    }
}
