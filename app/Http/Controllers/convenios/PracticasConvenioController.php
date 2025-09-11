<?php

namespace App\Http\Controllers\convenios;

use App\Exports\MatrizPracticasConvenioExport;
use App\Models\convenios\ConvenioHistorialCostosPracticaEntity;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\convenios\Repository\HistorialCostosPracticaRepository;
use App\Http\Controllers\convenios\Repository\PracticasConvenioRepository;
use App\Imports\ConvenioPracticasImport;
use App\Models\convenios\ConveniosEntity;
use App\Models\convenios\ConveniosPracticasEntity;
use App\Models\liquidaciones\LiquidacionDetalleEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class PracticasConvenioController extends Controller
{
    public function postPracticasConvenios(Request $request)
    {
        try {
            DB::beginTransaction();
            $practicas = $request->practicas;
            $count = 0;
            $user = Auth::user();
            $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');

            foreach ($practicas as $item) {
                ConveniosPracticasEntity::create([
                    'id_identificador_practica' => $item['id_identificador_practica'],
                    'cod_convenio' => $request->convenio,
                    'monto_especialista' => 0,
                    'monto_gastos' => 0,
                    'monto_ayudante' => 0,
                    'vigente' => '1',
                    'tipo_carga' => 'Galeno',
                    'fecha_vigencia' => $request->fecha_vigencia,
                    'fecha_carga' => $fechaActual,
                    'cod_usuario_carga' => $user->cod_usuario,
                    'fecha_vigencia_hasta' => $request->fecha_vigencia_hasta
                ]);
                $count++;
            }

            DB::commit();
            return response()->json(["message" => "Se procesaron <b>" . $count . " registros</b> correctamente"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function updateMontoPracticaConvenio(HistorialCostosPracticaRepository $repository, Request $request)
    {
        try {
            DB::beginTransaction();
            $practica = ConveniosPracticasEntity::find($request->id_practica_convenio);
            $idHistorialCosto = $repository->findByPasarInactivoElMontoAnterior($practica, 'READECUACION', null);
            $practica->monto_especialista = $request->monto_especialista;
            $practica->monto_gastos = $request->monto_gastos;
            $practica->monto_ayudante = $request->monto_ayudante;
            $practica->por_recaudacion = $request->por_recaudacion;
            $practica->observaciones = $request->observaciones;
            $repository->findByGuardarHistorialCosto($practica, $request->fecha_inicia_contrato, $request->fecha_corte_contrato, '1', 'READECUACION', $idHistorialCosto);
            DB::commit();

            return response()->json(["message" => "Montos actualizados correctamente"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function postCargaMasivaPracticas(Request $request)
    {
        $nombre_archivo = null;
        $horaCarga = Carbon::now('America/Lima')->format('His');

        if ($request->hasFile('file')) {
            $convenio = ConveniosEntity::find($request->convenio);
            $fileStorage = $request->file('file');
            $nombre_archivo = 'IMPORT_' . $horaCarga . "." . $fileStorage->extension();
            $destinationPath = "public/imports";
            Storage::putFileAs($destinationPath, $fileStorage, $nombre_archivo);
            $import = new ConvenioPracticasImport($request->convenio, $convenio->fecha_inicio, $convenio->fecha_fin);
            Excel::import($import, 'public/imports/' . $nombre_archivo);
            Storage::delete('public/imports/' . $nombre_archivo);
            if (count($import->practicasNoEncontradas) > 0) {
                $fileContent = Storage::get('public/logs/logs_imports_' . $request->convenio . '.xlsx');
                $fileMimeType = Storage::mimeType('public/logs/logs_imports_' . $request->convenio . '.xlsx');
                Storage::delete('public/logs/logs_imports_' . $request->convenio . '.xlsx');
                return response($fileContent, 200)
                    ->header('Content-Type', $fileMimeType);
            }

            return response()->json([
                'message' => 'Archivo subido correctamente'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Seleccione un archivo a importar'
            ], 409);
        }
    }

    public function postDeletePracticaConvenio($id)
    {
        $practica = ConveniosPracticasEntity::where('id_practica_convenio',$id)->first();
        if (!LiquidacionDetalleEntity::where('id_identificador_practica', $practica->id_identificador_practica)->exists()) {
            DB::delete('DELETE FROM tb_convenios_historial_costos WHERE cod_convenio = ? AND id_identificador_practica = ? ', [$practica->cod_convenio, $practica->id_identificador_practica]);
            $practica->delete();
        }

        return response()->json([
            'message' => 'registro eliminado correctamente'
        ], 200);
    }

    public function getAplicarAjusteLineal(HistorialCostosPracticaRepository $repository, Request $request)
    {
        try {
            DB::beginTransaction();
            if ($request->aplicar == '1') {
                $convenio = ConveniosEntity::find($request->convenio);
                $data = [];
                $fechaEjecutar = Carbon::parse($request->fecha_vigencia);
                $fechaFinContrato = Carbon::parse($convenio->fecha_fin);

                if ($repository->findByExistUltimoVigente($request->convenio, $request->fecha_vigencia)) {
                    DB::rollBack();
                    return response()->json(["message" => "La fecha de vigencia debe ser mayor al ultimo grupo cargado anteriormente"], 409);
                }

                if ($fechaEjecutar->gt($fechaFinContrato)) {
                    DB::rollBack();
                    return response()->json(["message" => "La fecha de vigencia debe ser menor a la fecha del contrato <b>" . $fechaFinContrato->format('d/m/Y') . "</b>"], 409);
                }

                if (count($request->detalle) > 0) {
                    $data = ConveniosPracticasEntity::where('cod_convenio', $request->convenio)
                        ->whereIn('id_identificador_practica', $request->detalle)
                        ->where('vigente', '1')
                        ->get();
                } else {
                    $data = ConveniosPracticasEntity::where('cod_convenio', $request->convenio)
                        ->where('vigente', '1')
                        ->get();
                }

                foreach ($data as $value) {
                    $valorProcentaje = $request->monto / 100;
                    if ($request->pago == '1') {
                        $monto = $value->monto_especialista;
                        $montoAumento = $monto * $valorProcentaje;
                        $monto = $monto + $montoAumento;
                        $repository->findByAumetoLinealExistenteDelDia($value, $monto, $request->monto, '1', 'LINEAL', $request->fecha_vigencia);
                        $repository->findByGuardarMontoAnteriorLineal($value, '0', 'LINEAL', $request->fecha_vigencia);
                        if ($monto > 0 && !$repository->findByExistsCostoPracticaLineal($value->id_identificador_practica, $value->id_practica_convenio, '1')) {
                            $repository->findBySaveCostosLineal(
                                $value,
                                $request->fecha_vigencia,
                                $convenio->fecha_fin,
                                $request->monto,
                                $monto,
                                '1',
                                'LINEAL'
                            );
                        }
                    } else if ($request->pago == '2') {
                        $monto = $value->monto_ayudante;
                        $montoAumento = $monto * $valorProcentaje;
                        $monto = $monto + $montoAumento;
                        $repository->findByAumetoLinealExistenteDelDia($value, $monto, $request->monto, '2', 'LINEAL', $request->fecha_vigencia);
                        $repository->findByGuardarMontoAnteriorLineal($value, '0', 'LINEAL', $request->fecha_vigencia);
                        if ($monto > 0 && !$repository->findByExistsCostoPracticaLineal($value->id_identificador_practica, $value->id_practica_convenio, '1')) {
                            $repository->findBySaveCostosLineal(
                                $value,
                                $request->fecha_vigencia,
                                $convenio->fecha_fin,
                                $request->monto,
                                $monto,
                                '2',
                                'LINEAL'
                            );
                        }
                    } else if ($request->pago == "4") {
                        $monto = $value->monto_gastos;
                        $montoAumento = $monto * $valorProcentaje;
                        $monto = $monto + $montoAumento;
                        $repository->findByAumetoLinealExistenteDelDia($value, $monto, $request->monto, '3', 'LINEAL', $request->fecha_vigencia);
                        $repository->findByGuardarMontoAnteriorLineal($value, '0', 'LINEAL', $request->fecha_vigencia);
                        if ($monto > 0 && !$repository->findByExistsCostoPracticaLineal($value->id_identificador_practica, $value->id_practica_convenio, '1')) {
                            $repository->findBySaveCostosLineal(
                                $value,
                                $request->fecha_vigencia,
                                $convenio->fecha_fin,
                                $request->monto,
                                $monto,
                                '3',
                                'LINEAL'
                            );
                        }
                    }
                }
            }
            DB::commit();
            return response()->json([
                'message' => 'Ajuste lineal aplicado correctamente a <b>' . count($data) . '</b> registro(s).'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function eliminarPracticaMasivo(Request $request)
    {
         DB::beginTransaction();
        try {
           $detalleEliminar = $request->input('detalle');
            $cod_convenio = null;
            $items = ConveniosPracticasEntity::whereIn('id_practica_convenio', $detalleEliminar)->get();
            foreach ($items as $item) {
                //$item = ConveniosPracticasEntity::where('id_practica_convenio',$practica)->first();
                if (!LiquidacionDetalleEntity::where('id_identificador_practica', $item->id_identificador_practica)->exists()) {
                    DB::delete(
                        'DELETE FROM tb_convenios_historial_costos
                   WHERE cod_convenio = ? AND id_identificador_practica = ? AND fecha_inicio = ? AND fecha_fin = ? ',
                        [$item->cod_convenio, $item->id_identificador_practica, $item->fecha_vigencia, $item->fecha_vigencia_hasta]
                    );
                    $item->delete();
                }
                $cod_convenio = $item->cod_convenio;
            }
            //@SI EL GRUPO SE ELIMINA, NO PODEMOS DEJAR LA MATRIZ SIN NINGUN GRUPO ACTIVO ASI QUE PASAMOS ACTIVO AL ULTIMO GRUPO QUE ESTABA VIGENTE
            if (!is_null($cod_convenio)) {
                $convenio =  ConveniosEntity::find($cod_convenio);
                $data = DB::select("SELECT fecha_vigencia,count(*) as cantidad FROM tb_convenios_practicas WHERE cod_convenio = ? group by fecha_vigencia order by fecha_vigencia desc", [$cod_convenio]);
                if (count($data) === 1) {
                    DB::update("UPDATE tb_convenios_historial_costos SET vigente = 1, fecha_fin = ? WHERE cod_convenio = ?", [$convenio->fecha_fin, $cod_convenio]);
                    DB::update("UPDATE tb_convenios_practicas SET vigente = 1, fecha_vigencia_hasta = ? WHERE cod_convenio = ?", [$convenio->fecha_fin, $cod_convenio]);
                } else if (count($data) > 1) {
                    $ultimoGrupoPracticas = DB::table("tb_convenios_practicas")
                        ->where('cod_convenio', $cod_convenio)
                        ->orderByDesc('id_practica_convenio')
                        ->limit(1)
                        ->first();
                    DB::update(
                        "UPDATE tb_convenios_historial_costos SET vigente = 1, fecha_fin = ? WHERE cod_convenio = ? AND fecha_inicio = ? AND fecha_fin = ?",
                        [$convenio->fecha_fin, $cod_convenio, $ultimoGrupoPracticas->fecha_vigencia, $ultimoGrupoPracticas->fecha_vigencia_hasta]
                    );
                    DB::update(
                        "UPDATE tb_convenios_practicas SET vigente = 1 , fecha_vigencia_hasta = ? WHERE cod_convenio = ? AND fecha_vigencia = ? AND fecha_vigencia_hasta = ? ",
                        [$convenio->fecha_fin, $cod_convenio, $ultimoGrupoPracticas->fecha_vigencia, $ultimoGrupoPracticas->fecha_vigencia_hasta]
                    );
                }
            }


            DB::commit();
            return response()->json(["message" => "Registros eliminados correctamente"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getListarPracticasConvenioPrestador(PracticasConvenioRepository $repository, Request $request)
    {
        $data = [];

        if (!empty($request->codigo) && empty($request->descripcion)) {
            $data = $repository->findByListCodigoPracticasPrestador($request);
        } else if (empty($request->codigo) && !empty($request->descripcion)) {
            $data = $repository->findByListDescripcionPracticasPrestador($request);
        } else {
            $data = $repository->findByListPracticasPrestador($request);
        }

        return response()->json($data);
    }

    public function getObtenerCostoPractica(PracticasConvenioRepository $repository, Request $request)
    {
        return response()->json($repository->findByCostoPracticaConvenio($request->practica, $request->periodo,$request->cod_convenio,$request->cod_prestador));
    }

    public function getAgrupadorFechaPracticas(Request $request)
    {
        $data = DB::select("SELECT fecha_vigencia,count(*) as cantidad FROM tb_convenios_practicas WHERE cod_convenio = ? group by fecha_vigencia order by fecha_vigencia desc", [$request->convenio]);
        return response()->json($data);
    }

    public function getInsertarObservacion(Request $request)
    {
        $practica = ConveniosPracticasEntity::find($request->id);
        $practica->observaciones = $request->observaciones;
        $practica->update();

        $historico = ConvenioHistorialCostosPracticaEntity::where('cod_convenio', $practica->cod_convenio)
            ->where('id_identificador_practica', $practica->id_identificador_practica)
            ->whereDate('fecha_inicio', $practica->fecha_vigencia)
            ->whereDate('fecha_fin', $practica->fecha_vigencia_hasta)
            ->first();
        $historico->observaciones = $request->observaciones;
        $historico->update();

        return response()->json(["message" => "Observación registrada correctamente"]);
    }

    public function getExportarMatrizPractica(Request $request)
    {
        return Excel::download(new MatrizPracticasConvenioExport($request->grupo, $request->convenio), 'matriz_convenio.xlsx');
    }
}
