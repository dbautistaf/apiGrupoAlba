<?php

namespace App\Http\Controllers\Tesoreria\Services;

use App\Http\Controllers\Tesoreria\Repository\TesExtractoBancariosRepository;
use App\Imports\ExtractoBancarioBancoNacionImport;
use App\Imports\ExtractoBancarioBbvaImport;
use App\Imports\ExtractoBancarioMacroImport;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TesExtractosBacariosController extends Controller
{
    public function getImportarExtracto(Request $request, TesExtractoBancariosRepository $repoExtracto)
    {
        try {
            $data = json_decode($request->data);
            $archivo = $request->file('archivo');
            $message = null;
            if ($archivo) {
                DB::beginTransaction();
                // Se usa el modelo unificado de Cygnus para todos los bancos
                $importacion = new \App\Imports\ExtractoBancarioCygnusImport($data->id_entidad_bancaria, $data->observaciones, $data->id_locatario);
                Excel::import($importacion, $archivo);
                $message = $importacion->message;

                if ($message == 'INVALID') {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'El archivo seleccionado no cumple con la estructura del modelo unificado.'
                    ], 409);
                }

                DB::commit();
                return response()->json(['message' => 'Archivo importado correctamente ']);
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'Se necesita adjuntar un archivo para continuar.'
                ], 409);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getFiltrar(Request $request, TesExtractoBancariosRepository $repoExtracto)
    {
        return response()->json($repoExtracto->findByList($request->desde, $request->hasta));
    }
}
