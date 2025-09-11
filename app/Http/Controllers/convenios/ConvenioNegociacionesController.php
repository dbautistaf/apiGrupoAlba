<?php

namespace App\Http\Controllers\convenios;

use App\Http\Controllers\convenios\Repository\ConvNegociacionesRepository;
use App\Models\convenios\ConvenioDetalleNegociacionContratoEntity;
use App\Models\convenios\ConvenioNegociacionContratoEntity;
use App\Models\convenios\ConvenioNegociacionRespuestasEntity;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConvenioNegociacionesController extends Controller
{
    public function postCrearNegociacion(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            $negociacion = ConvenioNegociacionContratoEntity::create([
                'id_tipo_propuesta' => $request->id_tipo_propuesta,
                'cod_convenio' => $request->cod_convenio,
                'cod_prestador' => $request->cod_prestador,
                'id_usuario' => $user->cod_usuario,
                'id_sector' => $request->id_sector,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'estado' => $request->estado,
                'valor' => $request->valor,
                'observaciones' => $request->observaciones
            ]);

            DB::commit();
            return response()->json(["message" => "Negociación creado con éxito", "data" => $negociacion]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function postActualizarNegociacion(Request $request)
    {
        try {
            DB::beginTransaction();
            $negociacion = ConvenioNegociacionContratoEntity::find($request->id_negociacion);
            $negociacion->id_tipo_propuesta = $request->id_tipo_propuesta;
            $negociacion->cod_convenio = $request->cod_convenio;
            $negociacion->cod_prestador = $request->cod_prestador;
            $negociacion->id_sector = $request->id_sector;
            $negociacion->fecha_inicio = $request->fecha_inicio;
            $negociacion->fecha_fin = $request->fecha_fin;
            $negociacion->estado = $request->estado;
            $negociacion->valor = $request->valor;
            $negociacion->observaciones = $request->observaciones;
            $negociacion->update();

            DB::commit();
            return response()->json(["message" => "Negociación actualizada con éxito", "data" => $negociacion]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function postCrearDetalleNegociacion(ConvNegociacionesRepository $repo, Request $request)
    {
        try {
            DB::beginTransaction();
            $data = json_decode($request->data);

            $existPractica = ConvenioDetalleNegociacionContratoEntity::where('id_identificador_practica', $data->id_identificador_practica)
                ->exists();

            if ($existPractica) {
                DB::rollBack();
                return response()->json([
                    'message' => "La practica <b>" . $data->practica . "</b> ya existe en el detalle"
                ], 409);
            }
            $nombreArchivo = $repo->findByArchivo($request);
            ConvenioDetalleNegociacionContratoEntity::create([
                'id_negociacion' => $data->id_negociacion,
                'cantidad' => $data->cantidad,
                'precio_unitario' => $data->precio_unitario,
                'precio_total' => $data->precio_unitario * $data->cantidad,
                'url_adjunto' =>  $nombreArchivo,
                'observaciones' => $data->observaciones,
                'id_identificador_practica' => $data->id_identificador_practica
            ]);

            DB::commit();
            return response()->json(["message" => "Detalle agregado con éxito"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function postNuevaRespuestaNegociacion(Request $request)
    {
        ConvenioNegociacionRespuestasEntity::create([
            'id_negociacion' => $request->id_negociacion,
            'id_tipo_respuesta' => $request->id_tipo_respuesta,
            'fecha_registra' => $request->fecha_registra,
            'observaciones' => $request->observaciones,
        ]);

        return response()->json(["message" => "Respuesta agregada con éxito"]);
    }

    public function eliminarItemDetalle(Request $request)
    {
        $item = ConvenioDetalleNegociacionContratoEntity::find($request->id_detalle_negociacion);
        $item->delete();
        return response()->json(["message" => "Registro eliminado con éxito"]);
    }

    public function getListarNegociaciones(Request $request)
    {
        $data = [];
        $data = ConvenioNegociacionContratoEntity::with([
            'prestador',
            'tipoPropuesta',
            'tipoSector'
        ])
            ->where('cod_convenio', $request->cod_convenio)
            ->get();

        return response()->json($data, 200);
    }

    public function getListarDetalleNegociaciones(Request $request)
    {
        $data = [];
        $data = ConvenioDetalleNegociacionContratoEntity::with(['practica'])
            ->where('id_negociacion', $request->id_negociacion)
            ->get();

        return response()->json($data, 200);
    }

    public function getListarRespuestasNegociaciones(Request $request)
    {
        $data = [];
        $data = ConvenioNegociacionRespuestasEntity::with(['tipo'])
            ->where('id_negociacion', $request->id_negociacion)
            ->get();
        return response()->json($data, 200);
    }

    public function eliminarItemRespuesta(Request $request)
    {
        $item = ConvenioNegociacionRespuestasEntity::find($request->id_negociacion_respuesta);
        $item->delete();
        return response()->json(["message" => "Registro eliminado con éxito"]);
    }

    public function eliminarNegociacion(Request $request)
    {
        DB::delete("DELETE FROM tb_convenios_detalle_negociacion WHERE id_negociacion = ? ", [$request->id_negociacion]);
        // DB::delete('delete tb_convenios_detalle_negociacion where id_negociacion = ?', [$request->id_negociacion]);
        DB::delete("DELETE FROM tb_convenios_negociacion_respuestas WHERE id_negociacion = ? ", [$request->id_negociacion]);

        $negociacion = ConvenioNegociacionContratoEntity::find($request->id_negociacion);
        $negociacion->delete();

        return response()->json(["message" => "Registro eliminado con éxito"]);
    }
}
