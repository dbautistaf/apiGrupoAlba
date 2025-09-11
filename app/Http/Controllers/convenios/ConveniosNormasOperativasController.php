<?php

namespace App\Http\Controllers\convenios;

use App\Models\convenios\ConveniosNormasOperativasEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ConveniosNormasOperativasController extends Controller
{
    public function postCargarNormaOperativa(Request $request)
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
                $fileStorage = $request->file('archivo'); // $file->storeAs('public', $fileName);
                $nombre_archivo = 'CONVENIO_NORM_OPE_' . $model->convenio . "_" . $horaCarga . "_AF_" . $anioActual . "." . $fileStorage->extension();
                $destinationPath = "public/convenios/normas";
                Storage::putFileAs($destinationPath, $fileStorage, $nombre_archivo);
            }

            ConveniosNormasOperativasEntity::create([
                'nombre_archivo' => $nombre_archivo,
                'observacion' => $model->observaciones,
                'cod_usuario' => $user->cod_usuario,
                'fecha_crea' => $fechaActual,
                'cod_convenio' => $model->convenio
            ]);


            DB::commit();
            return response()->json(["message" => "Norma operativa cargada correctamente"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getListarNormasOperativas(Request $request)
    {
        $data = [];
        $data = ConveniosNormasOperativasEntity::where('cod_convenio', $request->cod_convenio)
            ->orderByDesc('cod_norma_operativa')->get();

        return response()->json($data, 200);
    }

    public function eliminarNormaOperativa(Request $request)
    {

        $data = ConveniosNormasOperativasEntity::find($request->cod_norma_operativa);
        if (!is_null($request->nombre_archivo) && Storage::exists('public/convenios/normas/' . $request->nombre_archivo)) {
            Storage::delete('public/convenios/normas/' . $data->nombre_archivo);
        }
        $data->delete();

        return response()->json(["message" => "Registro eliminado correctamente"], 200);
    }

    public function getObtenerArchivo(Request $request)
    {
        if (!Storage::exists('public/convenios/normas/' . $request->archivo)) {
            return response()->json(['error' => 'File not found.'], Response::HTTP_NOT_FOUND);
        }

        $fileContent = Storage::get('public/convenios/normas/' . $request->archivo);
        $fileMimeType = Storage::mimeType('public/convenios/normas/' . $request->archivo);

        return response($fileContent, 200)
            ->header('Content-Type', $fileMimeType);
    }
}
