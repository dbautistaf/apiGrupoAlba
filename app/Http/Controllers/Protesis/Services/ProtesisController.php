<?php

namespace App\Http\Controllers\Protesis\Services;

use App\Http\Controllers\Protesis\Repository\ProtesisRepository;
use App\Http\Controllers\Utils\ManejadorDeArchivosUtils;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProtesisController extends Controller
{

    public function getProcesar(ProtesisRepository $repoProtesis, ManejadorDeArchivosUtils $repoArchivos, Request $request)
    {
        DB::beginTransaction();
        try {
            $message = '';
            $parameters = json_decode($request->data);
            if (is_numeric($parameters->id_protesis)) {
                // @UPDATE
                $archivosAdjuntos =  $repoArchivos->findByCargaMasivaArchivos('FILE_PROTESIS_' . $parameters->dni_afiliado, 'protesis/solicitar/', $request);
                $protesis = $repoProtesis->findByUpdate($parameters, $parameters->id_protesis, '');
                if ($archivosAdjuntos != null) {
                    $repoProtesis->findByAgregarDetalleArchivos($archivosAdjuntos, $protesis->id_protesis);
                }
                $repoProtesis->findByUpdateDetalle($parameters->detalle, $protesis->id_protesis);
                $message = 'Registro actualizado correctamente.';
            } else {
                // @CREATE
                $archivosAdjuntos = $repoArchivos->findByCargaMasivaArchivos('PROTESIS_' . $parameters->dni_afiliado, 'protesis/solicitar/', $request);
                $protesis = $repoProtesis->findBySave($parameters, '');
                if ($archivosAdjuntos != null) {
                    $repoProtesis->findByAgregarDetalleArchivos($archivosAdjuntos, $protesis->id_protesis);
                }
                $repoProtesis->findByAgregarDetalleArchivos($archivosAdjuntos, $protesis->id_protesis);
                $repoProtesis->findBySaveDetalle($parameters->detalle, $protesis->id_protesis);
                $message = 'Registro procesado correctamente.';
            }
            DB::commit();
            return response()->json(["message" => $message], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getEliminar(ProtesisRepository $repoProtesis, Request $request)
    {
        DB::beginTransaction();
        try {
            $repoProtesis->findByDeleteProtesis($request->id);
            DB::commit();
            return response()->json(["message" => "Registro eliminado correctamente"], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getBuscarId(ProtesisRepository $repoProtesis, Request $request)
    {
        return response()->json($repoProtesis->findById($request->id));
    }
}
