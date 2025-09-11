<?php

namespace App\Http\Controllers\convenios;

use App\Models\convenios\ConveniosDocumentacionEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ConveniosDocumentacionController extends Controller
{
    public function postCargarDocumentacion(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
            $model = json_decode($request->model);
            $anioActual = Carbon::now('America/Lima')->year;
            $horaCarga = Carbon::now('America/Lima')->format('H-i-s');

            $nombre_archivo = "ola.php";

            if ($request->hasFile('archivo')) {
                $fileStorage = $request->file('archivo');
                $nombre_archivo = 'CONV_DOCUMENT_' . $model->convenio . "_" . $horaCarga . "_AF_" . $anioActual . "." . $fileStorage->extension();
                $destinationPath = "public/convenios/documentacion";
                Storage::putFileAs($destinationPath, $fileStorage, $nombre_archivo);
            }

            ConveniosDocumentacionEntity::create([
                'nombre_archivo' => $nombre_archivo,
                'observaciones' => $model->observaciones,
                'cod_usuario' => $user->cod_usuario,
                'fecha_crea' => $fechaActual,
                'cod_convenio' => $model->convenio
            ]);


            DB::commit();
            return response()->json(["message" => "Documentación cargada correctamente"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getListarDocumentacion(Request $request)
    {
        $data = [];
        $data = ConveniosDocumentacionEntity::where('cod_convenio', $request->cod_convenio)
            ->orderByDesc('cod_documentacion')->get();

        return response()->json($data, 200);
    }

    public function eliminarDocumentacion(Request $request)
    {

        $data = ConveniosDocumentacionEntity::find($request->cod_documentacion);
        if (!is_null($request->nombre_archivo) && Storage::exists('public/convenios/documentacion/' . $request->nombre_archivo)) {
            Storage::delete('public/convenios/documentacion/' . $data->nombre_archivo);
        }

        $data->delete();

        return response()->json(["message" => "Registro eliminado correctamente"], 200);
    }

    public function getObtenerArchivoDocumentacion(Request $request)
    {
        if (!Storage::exists('public/convenios/documentacion/' . $request->archivo)) {
            return response()->json(['error' => 'File not found.'], Response::HTTP_NOT_FOUND);
        }

        $fileContent = Storage::get('public/convenios/documentacion/' . $request->archivo);
        $fileMimeType = Storage::mimeType('public/convenios/documentacion/' . $request->archivo);

        return response($fileContent, 200)
            ->header('Content-Type', $fileMimeType);
    }
}
