<?php

namespace App\Http\Controllers\Utils;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UtilsController extends Controller
{


    protected $repoCorrelativos;

    public function __construct(CorrelativosOspfRepository $repoCorrelativos)
    {
        $this->repoCorrelativos = $repoCorrelativos;
    }

    public function getObtenerCorrelativo(Request $request)
    {
        $data = null;

        if ($request->desc == 'SI') {
            $data =  $this->repoCorrelativos->findByObtenerCorrelativoAndAbreviatura($request->tipo);
        } else {
            $data =  $this->repoCorrelativos->findByObtenerCorrelativo($request->tipo);
        }
        return response()->json($data);
    }
}
