<?php

namespace App\Http\Controllers;

use App\Models\MesaEntradaModelo;
use App\Models\TipoAreaModelo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReportesController extends Controller
{
    //

    public function srvMesaEntrada(Request $request)
    {
        Pdf::setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        $data = [];

        if (!is_null($request->sindicato) && is_null($request->area)) {
            $data = MesaEntradaModelo::with(['tipoDocumento', 'tipoArea', 'sindicato'])
                ->where('cod_sindicato', $request->sindicato)
                ->whereBetween('fecha_carga', [$request->desde, $request->hasta])
                ->get();
        } else if (is_null($request->sindicato) && !is_null($request->area)) {
            $data = MesaEntradaModelo::with(['tipoDocumento', 'tipoArea', 'sindicato'])
                ->where('cod_tipo_area', $request->area)
                ->get();
        } else if (!is_null($request->sindicato) && !is_null($request->area)) {
            $data = MesaEntradaModelo::with(['tipoDocumento', 'tipoArea', 'sindicato'])
                ->where('cod_sindicato', $request->sindicato)
                ->where('cod_tipo_area', $request->area)
                ->get();
        } else {
            $data = MesaEntradaModelo::with(['tipoDocumento', 'tipoArea', 'sindicato'])
            ->whereBetween('fecha_carga', [$request->desde, $request->hasta])
            ->get();
        }

        $description = "";
        if(!is_null($request->area)){
           $dt = TipoAreaModelo::find($request->area);
           $description = $dt->tipo_area;
        }

        $pdf = Pdf::loadView('rpt_mesa_entrada', ["data" => $data, "desde" => $request->desde, "hasta" => $request->hasta, "area" =>  $description ]);

        $pdf->setPaper('A4', 'landscape');

        //return $pdf->download('mesaEntrada.pdf'); 
        return response()->json($data, 200);
    }
}
