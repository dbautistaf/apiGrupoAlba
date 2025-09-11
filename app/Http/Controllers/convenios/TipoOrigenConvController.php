<?php

namespace App\Http\Controllers\convenios;


use App\Http\Controllers\convenios\Repository\TipoOrigenConvRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TipoOrigenConvController extends Controller
{
    public function getListarData(TipoOrigenConvRepository $repo, Request $request)
    {
        return response()->json($repo->findByListAlls($request->vigente), 200);
    }

}
