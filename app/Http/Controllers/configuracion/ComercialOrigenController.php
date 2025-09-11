<?php

namespace App\Http\Controllers\configuracion;

use App\Http\Controllers\Controller;
use App\Models\ComercialOrigenModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as RoutingController;

class ComercialOrigenController extends RoutingController
{
    //
    public function getListaComercialOrigen()
    {
        return ComercialOrigenModel::with(['comercial_caja','locatario'])->get();
    }

    public function saveComercialOrigen(Request $request)
    {
        if ($request->id_comercial_origen) {
            $query = ComercialOrigenModel::where('id_comercial_origen', $request->id_comercial_origen)->first();
            $query->detalle_comercial_origen = $request->detalle_comercial_origen;
            $query->id_comercial_caja = $request->id_comercial_caja;
            $query->id_locatario = $request->id_locatario;
            $query->save();
            return response()->json(['message' => 'Datos del Origen actualizado correctamente'], 200);
        } else {
            ComercialOrigenModel::create([
                'detalle_comercial_origen' => $request->detalle_comercial_origen,
                'id_comercial_caja' => $request->id_comercial_caja,
                'id_locatario'=>$request->id_locatario,
                'activo' => 1
            ]);
            return response()->json(['message' => 'Datos del Origen registrados correctamente'], 200);
        }
    }

    public function updateEstado(Request $request)
    {
        ComercialOrigenModel::where('id_comercial_origen', $request->id)->update(['activo' => $request->activo,]);
        return response()->json(['message' => 'Estado cambiado correctamente'], 200);
    }

    public function getIdComercilaOrigen($id)
    {
        return ComercialOrigenModel::where('id_comercial_origen', $id)->first();
    }
}
