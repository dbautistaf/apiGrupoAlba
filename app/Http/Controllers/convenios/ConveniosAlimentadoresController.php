<?php

namespace App\Http\Controllers\convenios;

use App\Http\Controllers\convenios\Dto\AltasCategoriasConvenioDTo;
use App\Models\convenios\ConveniosAlicuotaIvaEntity;
use App\Models\convenios\ConveniosAltaCategoriaEntity;
use App\Models\convenios\ConveniosCategoriaPagosEntity;
use App\Models\convenios\ConveniosTipoCBUEntity;
use App\Models\convenios\ConveniosTipoMedioPagoEntity;
use App\Models\convenios\ConveniosTipoValorizacionEntity;
use App\Models\convenios\ConvenioTipoComprobanteEntity;
use Illuminate\Routing\Controller;

class ConveniosAlimentadoresController extends Controller
{

    public function getListarCategoriaPagos()
    {
        try {
            $data =   ConveniosCategoriaPagosEntity::where('vigente', '1')
                ->orderBy('descripcion')
                ->get();

            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getListarAltaCategorias()
    {
        try {
            $data =   ConveniosAltaCategoriaEntity::where('vigente', '1')
                ->orderBy('descripcion')
                ->get()
                ->map(function ($row) {
                    return new AltasCategoriasConvenioDTo($row->id_alta_categoria, $row->descripcion, 'seleccionar todas');
                });

            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getListarTipoValorizacion()
    {
        try {
            $data =   ConveniosTipoValorizacionEntity::where('vigente', '1')
                ->orderBy('descripcion')
                ->get();

            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getListarPrestadorTipoComprobante()
    {
        try {
            $data =   ConvenioTipoComprobanteEntity::orderBy('descripcion')
                ->get();

            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getListarAlicuotaIva()
    {
        try {
            $data =   ConveniosAlicuotaIvaEntity::orderBy('descripcion')
                ->get();

            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getListarTipoMediosPago()
    {
        try {
            $data =   ConveniosTipoMedioPagoEntity::orderBy('descripcion')
                ->get();

            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getListarTipoCBU()
    {
        try {
            $data =   ConveniosTipoCBUEntity::orderBy('descripcion')
                ->get();

            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
