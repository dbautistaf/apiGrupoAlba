<?php

namespace App\Http\Controllers\configuracion;

use App\Http\Controllers\Controller;
use App\Models\ComercialCajaModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Auth;

class ComercialCajaController extends RoutingController
{
    //
    public function getListaComercialCaja(){
        return ComercialCajaModel::with(['locatario','gerenciadora'])->get();
    }

    public function saveComercialCaja(Request $request){
        if($request->id_comercial_caja){
            $query=ComercialCajaModel::where('id_comercial_caja', $request->id_comercial_caja)->first();
            $query->nros=$request->nros;
            $query->detalle_comercial_caja=$request->detalle_comercial_caja;
            $query->id_gerenciadora=$request->id_gerenciadora;
            $query->save();
            return response()->json(['message' => 'Datos de Obra Social actualizado correctamente'], 200);
        }else{
            ComercialCajaModel::create([
                'nros'=>$request->nros,
                'detalle_comercial_caja'=>$request->detalle_comercial_caja,
                'id_locatario'=>$request->id_locatario,
                'id_gerenciadora'=>$request->id_gerenciadora,
                'activo' =>1
            ]);
            return response()->json(['message' => 'Datos de Obra Social registrados correctamente'], 200);
        }
    }

    public function updateEstado(Request $request)
    {
        ComercialCajaModel::where('id_comercial_caja', $request->id)->update(['activo' => $request->activo,]);
        return response()->json(['message' => 'Estado cambiado correctamente'], 200);
    }

    public function getIdComercilaCaja($id)
    {
        return ComercialCajaModel::where('id_comercial_caja', $id)->first();
    }

    public function deleteComercialCaja(Request $request)
    {
        $item = ComercialCajaModel::find($request->id);
        
        if (!$item) {
            return response()->json(['message' => 'Obra Social no encontrada'], 404);
        }

        if ($item->activo == 1) {
            return response()->json(['message' => 'No se puede eliminar una Obra Social activa. Por favor, desactívela primero.'], 400);
        }

        // Registramos el usuario que elimina
        $item->cod_usuario_elimina = Auth::id();
        $item->save();

        // Borrado lógico (SoftDelete)
        $item->delete();

        return response()->json(['message' => 'Obra Social eliminada correctamente'], 200);
    }
}
