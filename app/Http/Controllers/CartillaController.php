<?php

namespace App\Http\Controllers;

use App\Models\CartillaModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CartillaController extends Controller
{
    //
    public function getCartilla()
    {
        $query = CartillaModelo::where('activo', 1)->get();
        return response()->json($query, 200);
    }

    public function getIdCartilla($id)
    {
        $query = CartillaModelo::where('id_cartilla', $id)->first();
        return response()->json($query, 200);
    }

    public function getfilterCartilla(Request $request)
    {
        $query = CartillaModelo::query();
        if (empty($request->cuit)) {
            if (!empty($request->localidad)) {
                $query->where('localidad', trim($request->localidad));
            }

            if (!empty($request->id_especialidad)) {
                $query->where('especialidades', trim($request->id_especialidad));
            }

            if (!empty($request->id_zona)) {
                $query->where('zona', trim($request->id_zona));
            }
            return $query->get();
        } else {
            // Si 'cuit' no está vacío, filtrar por 'prestador'
            return CartillaModelo::where('prestador', 'LIKE', "{$request->cuit}%")->get();
        }
    }

    public function postSaveCartilla(Request $request)
    {
        if ($request->id_cartilla != '') {
            $query = CartillaModelo::where('id_cartilla', $request->id_cartilla)->first();
            if ($query) {
                $query->prestador = $request->prestador;
                $query->tipo = $request->tipo;
                $query->calle = $request->calle;
                $query->piso = $request->piso;
                $query->nro = $request->nro;
                $query->telefono_turnos = $request->telefono_turnos;
                $query->whatsapp = $request->whatsapp;
                $query->localidad = $request->localidad;
                $query->zona = $request->zona;
                $query->especialidades = $request->especialidades;
                $query->activo = '1';
                $query->save();
                return response()->json(['message' => 'Prestador actualizado correctamente'], 200);
            }
        } else {
            CartillaModelo::create([
                'prestador' => $request->prestador,
                'tipo' => $request->tipo,
                'calle' => $request->calle,
                'nro' =>$request->nro,
                'piso' => $request->piso,
                'telefono_turnos' => $request->telefono_turnos,
                'whatsapp' => $request->whatsapp,
                'localidad' => $request->localidad,
                'zona' => $request->zona,
                'especialidades' => $request->especialidades,
                'activo' => '1'
            ]);
            return response()->json(['message' => 'Prestador registrado correctamente'], 200);
        }
    }

    public function postdeleteCartilla(Request $request)
    {
        CartillaModelo::where('id_cartilla', $request->id_cartilla)->delete();
        return response()->json(['message' => 'Prestador eliminado correctamente'], 200);
    }
}
