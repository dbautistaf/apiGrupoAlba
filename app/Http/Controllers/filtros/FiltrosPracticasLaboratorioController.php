<?php

namespace App\Http\Controllers\filtros;

use App\Models\DetalleGrupoPracticaLaboratorioEntity;
use App\Models\TipoMotivoRechazoAutotizacionEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FiltrosPracticasLaboratorioController extends Controller
{
    public function getBuscarTipoPractica(Request $request)
    {
        $data = [];

        if (!empty($request->search)) {
            if(is_numeric($request->search)){
                $data =  DetalleGrupoPracticaLaboratorioEntity::with(['practica', 'grupo'])
                ->whereHas('practica', function ($query) use ($request) {
                    $query->where('codigo_practica', 'like', "$request->search%");
                })
                ->limit(10)
                ->get();
            }else{
                $data =  DetalleGrupoPracticaLaboratorioEntity::with(['practica', 'grupo'])
                ->whereHas('practica', function ($query) use ($request) {
                    $query->where('descripcion_practica', 'like', "$request->search%");
                })
                ->limit(10)
                ->get();
            }
        } else {
            $data = DetalleGrupoPracticaLaboratorioEntity::with(['practica', 'grupo'])
                ->limit(10)
                ->orderByDesc('cod_detalle_practica_grupo')
                ->get();
        }
        return response()->json($data, 200);
    }

    public function getBuscarPracticaId($id)
    {
        $data = DetalleGrupoPracticaLaboratorioEntity::with(['practica', 'grupo'])
            ->where('cod_tipo_practica', $id)
            ->get();

        return response()->json($data, 200);
    }

    public function getListarMotivosRechazos()
    {
        $data =  TipoMotivoRechazoAutotizacionEntity::get();
        return response()->json($data, 200);
    }
}
