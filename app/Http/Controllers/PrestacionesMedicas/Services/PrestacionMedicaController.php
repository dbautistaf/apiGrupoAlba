<?php

namespace App\Http\Controllers\PrestacionesMedicas\Services;

use App\Exports\PrestacionMedicaExport;
use App\Http\Controllers\PrestacionesMedicas\Repository\PrestacionesmedicasFiltrosRepository;
use App\Http\Controllers\PrestacionesMedicas\Repository\PrestacionMedicaRepository;
use App\Http\Controllers\Utils\ManejadorDeArchivosUtils;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PrestacionMedicaController  extends Controller
{
    public function getAltaPrestacionMedica(ManejadorDeArchivosUtils $storagaFile, PrestacionMedicaRepository $repoPrestacionmedica, Request $request)
    {
        DB::beginTransaction();
        $nombreArchivo = "A";
        // $pathFileName = "";
        try {
            $prestacioMedica = json_decode($request->datos);

            $datosTramite = $repoPrestacionmedica->findBySaveDetalleTramite($prestacioMedica->datos_tramite);
            $prestacionDB = $repoPrestacionmedica->findBySave($prestacioMedica, $nombreArchivo, $datosTramite);
            $repoPrestacionmedica->findBySaveDetallePrestacion($prestacioMedica->detalle, $prestacionDB);

            if ($request->hasFile('archivos')) {
                $archivosAdjuntos = $storagaFile->findByCargaMasivaArchivos("PRESTACION_" . $prestacioMedica->dni_afiliado, 'prestaciones', $request);
                $repoPrestacionmedica->findBySavePrestacionMedicaFile($archivosAdjuntos, $prestacionDB->cod_prestacion);
            }

            if ($prestacioMedica->archivo_adjunto == 'REASIGNACION') {
                $repoPrestacionmedica->findByEliminarPrestacion($prestacioMedica->cod_prestacion);
            }

            DB::commit();
            return response()->json(["message" => "Prestación registrada correctamente."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            //  $storagaFile->findByDeleteFileName($pathFileName);
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getActualizarPrestacion(ManejadorDeArchivosUtils $storagaFile, PrestacionMedicaRepository $repoPrestacionmedica, Request $request)
    {
        DB::beginTransaction();
        $nombreArchivo = "A";
        //$pathFileName = "";
        try {

            $prestacionMedica = json_decode($request->datos);


            $datosTramite = $repoPrestacionmedica->findByUpdateDetalleTramite($prestacionMedica->datos_tramite);
            $prestacionDB = $repoPrestacionmedica->findByUpdateId($prestacionMedica, $nombreArchivo, $datosTramite);

            if ($request->hasFile('archivos') && count($request->archivos) > 0) {
                $archivosAdjuntos = $storagaFile->findByCargaMasivaArchivos("PRESTACION_" . $prestacionMedica->dni_afiliado, 'prestaciones', $request);
                $repoPrestacionmedica->findBySavePrestacionMedicaFile($archivosAdjuntos, $prestacionDB->cod_prestacion);
            }
            //  @GUARDAMOS EL NUEVO DETALLE
            $repoPrestacionmedica->findByUpdateDetallePrestacion($prestacionMedica->detalle, $prestacionDB);

            DB::commit();
            return response()->json(["message" => "Prestación actualizada correctamente."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            // $storagaFile->findByDeleteFileName($pathFileName);
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getConsultarPrestaciones(PrestacionesmedicasFiltrosRepository $repoFiltro, Request $request)
    {
        try {
            $data = [];
            if (is_numeric($request->search)) {
                if (strlen($request->search) == 8) {
                    $data =  $repoFiltro->findByListFechaRegistraBetweenAndDniAfiliado($request->desde, $request->hasta, $request->search, $request->tramite);
                } else if (strlen($request->search) == 11) {
                    $data = $repoFiltro->findByListFechaRegistraBetweenAndCuilAfiliado($request->desde, $request->hasta, $request->search, $request->tramite);
                } else {
                    $data = $repoFiltro->findByListFechaRegistraBetweenAndDniAfiliadoLike($request->desde, $request->hasta, $request->search, $request->tramite);
                }
            } else if (is_string($request->search)) {
                $data =  $data = $repoFiltro->findByListFechaRegistraBetweenAndNombresAfiliadoLike($request->desde, $request->hasta, $request->search, $request->tramite);
            } else if (!empty($request->estado)) {
                $data =  $data = $repoFiltro->findByListEstado($request->estado);
            } else {
                $data =  $repoFiltro->findByListFechaRegistraBetweenAndLimit($request->desde, $request->hasta, 200, $request->tramite);
            }

            foreach ($data as $objeto) {
                $objeto->setAttribute('show', false);
            }
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getBuscarPrestacionId(PrestacionesmedicasFiltrosRepository $repoFiltro, Request $request)
    {
        $data =  $repoFiltro->findById($request->code);
        return response()->json($data, 200);
    }

    public function getEstadoImprimirDetalle(PrestacionMedicaRepository $repo, Request $request)
    {
        $repo->findByUpdateEstadoImprimir($request->id, $request->estado);
        return response()->json(["message" => 'Item actualizado correctamente'], 200);
    }

    public function deleteEliminarPrestacion(PrestacionesmedicasFiltrosRepository $repoFiltro, ManejadorDeArchivosUtils $storagaFile, PrestacionMedicaRepository $repoPrestacionMedica, Request $request)
    {
        $prestacion = $repoFiltro->findById($request->id);

        //if ($prestacion->cod_tipo_estado == '2') {
        //DB::delete('DELETE FROM tb_prestaciones_medicas_detalle WHERE cod_prestacion = ?', [$request->id]);
        //DB::delete('DELETE FROM tb_prestaciones_medicas WHERE cod_prestacion = ?', [$request->id]);
        // $storagaFile->findByDeleteFileName("prestaciones/" . $prestacion->archivo_adjunto);
        $repoFiltro->findByDeleteId($request->id);
        //} else {
        //   return response()->json(["message" => "No se puede eliminar una prestación ya Autorizada o Rechazada."], 409);
        //}

        return response()->json(["message" => "Prestación eliminada correctamente."], 200);
    }

    public function getBuscarPrestacionAfiliado(PrestacionesmedicasFiltrosRepository $repoFiltro, Request $request)
    {
        return response()->json($repoFiltro->findByListDniAfiliado($request->dni));
    }

    public function getVerAdjunto(ManejadorDeArchivosUtils $storageFile, Request $request, PrestacionMedicaRepository $repoFiltro)
    {
        $path = "prestaciones/";
        $prestacion = $repoFiltro->findByObtenerAdjuntoId($request->id);
        $anioTrabaja = Carbon::parse($prestacion->fecha_carga)->year;
        $path .= "{$anioTrabaja}/$prestacion->archivo";
        return $storageFile->findByObtenerArchivo($path);
    }

    public function getEliminarItemDetalle(PrestacionMedicaRepository $repoFiltro, Request $request)
    {
        if ($repoFiltro->findByExisteAuditoriaItemDetalle($request->id)) {
            return response()->json(["success" => false, "message" => "No se puede eliminar un item que fue autorizado"], 409);
        }
        $repoFiltro->findByEliminarItemDetalle($request->id);

        return response()->json(["success" => true, "message" => "Registro eliminado correctamente"]);
    }

    public function getEliminarAdjunto(PrestacionMedicaRepository $repoFiltro, Request $request)
    {
        $repoFiltro->findByEliminarAdjunto($request->id);

        return response()->json(["success" => true, "message" => "Archivo eliminado correctamente"]);
    }

    public function getExportPrestacion(Request $request)
    {
        return Excel::download(new PrestacionMedicaExport($request), 'Prestaciones.xlsx');
    }

    public function getListPrestacion(PrestacionesmedicasFiltrosRepository $repoFiltro, Request $request)
    {
        return response()->json($repoFiltro->findByListAutorizacionLimit($request->shared));
    }

    public function getListPrestacionIds(PrestacionesmedicasFiltrosRepository $repoFiltro, Request $request)
    {

        $ids = $request->query('ids');
        if (!$ids || !is_array($ids)) {
            return response()->json([]);
        }

        return response()->json($repoFiltro->findByListAutorizacionIds($ids));
    }
}
