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
                if ($data->id_entidad_bancaria === 1) {
                    //@BANCO DE LA NACION
                    $importacion = new ExtractoBancarioBancoNacionImport($data->id_entidad_bancaria, $data->observaciones);
                    Excel::import($importacion, $archivo);
                    $message = $importacion->message;
                } else if ($data->id_entidad_bancaria == 2) {
                    //@BBVA (BANCO FRANCES)
                    $importacion = new ExtractoBancarioBbvaImport($data->id_entidad_bancaria, $data->observaciones);
                    Excel::import($importacion, $archivo);
                    $message = $importacion->message;
                } else if ($data->id_entidad_bancaria === 3) {
                    //@BANCO MACRO
                    $importacion = new ExtractoBancarioMacroImport($data->id_entidad_bancaria, $data->observaciones);
                    Excel::import($importacion, $archivo);
                    $message = $importacion->message;
                }

                if ($message == 'INVALID') {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'El archivo seleccionado no cumple con la estructura del banco seleccionado.'
                    ], 409);
                }

                DB::commit();
                return response()->json(['message' => "Archivo importado correctamente "]);
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
