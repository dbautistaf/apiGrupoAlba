<?php

namespace App\Http\Controllers\liquidaciones;

use App\Models\liquidaciones\LiquidacionDocumentosAdjuntosEntity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

class LiqDocumentosAdjuntosController extends Controller
{
    public function postCargarNormaOperativa(Request $request)
    {
        try {
            $user = Auth::user();
            $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
            $data = json_decode($request->data);
            $anioActual = Carbon::now('America/Argentina/Buenos_Aires')->year;
            $horaCarga = Carbon::now('America/Argentina/Buenos_Aires')->format('H-i-s');

            $doc_auditoria = null;
            if ($request->hasFile('doc_auditoria')) {
                $fileStorage = $request->file('doc_auditoria');
                $doc_auditoria = 'DOC_AUDITORIA' . $data->id_factura . "_" . $horaCarga . "_AF_" . $anioActual . "." . $fileStorage->extension();
                $destinationPath = "public/liquidaciones/doc_auditoria";
                Storage::putFileAs($destinationPath, $fileStorage, $doc_auditoria);
            }

            $doc_respaldo = null;
            if ($request->hasFile('doc_respaldo')) {
                $fileStorage = $request->file('doc_respaldo');
                $doc_respaldo = 'DOC_RESPALDO' . $data->id_factura . "_" . $horaCarga . "_AF_" . $anioActual . "." . $fileStorage->extension();
                $destinationPath = "public/liquidaciones/doc_respaldo";
                Storage::putFileAs($destinationPath, $fileStorage, $doc_respaldo);
            }

            $doc_prestaciones = null;
            if ($request->hasFile('doc_prestaciones')) {
                $fileStorage = $request->file('doc_prestaciones');
                $doc_prestaciones = 'DOC_PRESTACIONES' . $data->id_factura . "_" . $horaCarga . "_AF_" . $anioActual . "." . $fileStorage->extension();
                $destinationPath = "public/liquidaciones/doc_prestaciones";
                Storage::putFileAs($destinationPath, $fileStorage, $doc_prestaciones);
            }

            LiquidacionDocumentosAdjuntosEntity::create([
                'archivo_auditoria' => $doc_auditoria,
                'archivo_respaldo' => $doc_respaldo,
                'detalle_prestacion' => $doc_prestaciones,
                'id_factura' => $data->id_factura,
                'observaciones' => $data->observaciones,
                'fecha_carga' => $fechaActual,
                'cod_usuario' => $user->cod_usuario
            ]);


            return response()->json(["message" => "Documentacion Adjuntada correctamente"]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getEliminarArchivo(Request $request)
    {

        $data = LiquidacionDocumentosAdjuntosEntity::find($request->cod_documentacion);

        if ($request->tipo == 'audit' && !is_null($data->archivo_auditoria) && Storage::exists('public/liquidaciones/doc_auditoria/' . $data->archivo_auditoria)) {
            Storage::delete('public/liquidaciones/doc_auditoria/' . $data->archivo_auditoria);
            $data->archivo_auditoria = null;
        }

        if ($request->tipo == 'resp' && !is_null($data->archivo_respaldo) && Storage::exists('public/liquidaciones/doc_respaldo/' . $data->archivo_respaldo)) {
            Storage::delete('public/liquidaciones/doc_respaldo/' . $data->archivo_respaldo);
            $data->archivo_respaldo = null;
        }

        if ($request->tipo == 'prest' && !is_null($data->detalle_prestacion) && Storage::exists('public/liquidaciones/doc_prestaciones/' . $data->detalle_prestacion)) {
            Storage::delete('public/liquidaciones/doc_prestaciones/' . $data->detalle_prestacion);
            $data->detalle_prestacion = null;
        }


        $data->update();

        return response()->json(["message" => "Archivo eliminado correctamente"], 200);
    }

    public function getVerAdjunto(Request $request)
    {
        $data = LiquidacionDocumentosAdjuntosEntity::find($request->cod_documentacion);
        $fileContent = null;
        $fileMimeType = null;

        if ($request->tipo == 'audit' && !Storage::exists('public/liquidaciones/doc_auditoria/' . $data->archivo_auditoria)) {
            return response()->json(['error' => 'Archivo no econtrado.'], Response::HTTP_NOT_FOUND);
        } else {
            $fileContent = Storage::get('public/liquidaciones/doc_auditoria/' . $data->archivo_auditoria);
            $fileMimeType = Storage::mimeType('public/liquidaciones/doc_auditoria/' . $data->archivo_auditoria);
        }

        if ($request->tipo == 'resp' && !Storage::exists('public/liquidaciones/doc_respaldo/' . $data->archivo_respaldo)) {
            return response()->json(['error' => 'Archivo no econtrado.'], Response::HTTP_NOT_FOUND);
        } else {
            $fileContent = Storage::get('public/liquidaciones/doc_respaldo/' . $data->archivo_respaldo);
            $fileMimeType = Storage::mimeType('public/liquidaciones/doc_respaldo/' . $data->archivo_respaldo);
        }

        if ($request->tipo == 'prest' && !Storage::exists('public/liquidaciones/doc_prestaciones/' . $data->detalle_prestacion)) {
            return response()->json(['error' => 'Archivo no econtrado.'], Response::HTTP_NOT_FOUND);
        } else {
            $fileContent = Storage::get('public/liquidaciones/doc_prestaciones/' . $data->detalle_prestacion);
            $fileMimeType = Storage::mimeType('public/liquidaciones/doc_prestaciones/' . $data->detalle_prestacion);
        }

        return response($fileContent, 200)
            ->header('Content-Type', $fileMimeType);
    }

    public function getById(Request $request)
    {
        return response()->json(LiquidacionDocumentosAdjuntosEntity::find($request->id));
    }
}
