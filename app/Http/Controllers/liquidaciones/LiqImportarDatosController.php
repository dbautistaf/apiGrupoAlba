<?php

namespace App\Http\Controllers\liquidaciones;

use App\Http\Controllers\facturacion\repository\FacturasPrestadoresRepository;
use App\Http\Controllers\liquidaciones\repository\LiquidacionesRepository;
use App\Imports\ImportarLiquidacionesImport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class LiqImportarDatosController extends Controller
{

    public function getImportarLiquidaciones(
        LiquidacionesRepository $repo,
        FacturasPrestadoresRepository $repoFactura,
        Request $request
    ) {
        $liquidacion = null;
        $nombre_archivo = null;
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');

        if ($request->hasFile('file')) {
            $fileStorage = $request->file('file');
            $nombre_archivo = 'IMPORT_DET_' . $request->id_factura . "." . $fileStorage->extension();
            $destinationPath = "public/imports";

            Storage::putFileAs($destinationPath, $fileStorage, $nombre_archivo);

            $import = new ImportarLiquidacionesImport($request->id_factura);
            Excel::import($import, 'public/imports/' . $nombre_archivo);

            Storage::delete('public/imports/' . $nombre_archivo);

            if (count($import->practicasNoEncontradas) > 0) {
                $fileContent = Storage::get('public/logs/logs_imports_liquidaciones' . $request->id_factura . '.xlsx');
                $fileMimeType = Storage::mimeType('public/logs/logs_imports_liquidaciones' . $request->id_factura . '.xlsx');
                Storage::delete('public/logs/logs_imports_liquidaciones' . $request->id_factura . '.xlsx');
                return response($fileContent, 200)
                    ->header('Content-Type', $fileMimeType);
            }

            $dtListaDetalle = $import->detalleLiquidaciones;
            $facturaData = null;
            if ($repo->findByIdExists($request->id_factura)) {
                $liquidacion = $repo->findByLiquidacionPrimeraFactura($request->id_factura);
                $facturaData = $repo->findByCreateDetalleLiquidacion($dtListaDetalle, $liquidacion->id_liquidacion, $liquidacion->id_factura);
            } else {
                $facturaData = $repo->findByCreateDetalleLiquidacion($dtListaDetalle, null, $request->id_factura);
            }

            $sumDetalle = $repo->fidByObtenerTotalDebitadoFactura($request->id_factura);
            $totalFacturado = 0;
            $totalAprobado = 0;
            $totalDebitado = 0;
            $totalCoseguro = 0;

            if (!is_null($sumDetalle)) {
                $totalFacturado = $sumDetalle->total_facturado;
                $totalAprobado = $sumDetalle->total_aprobado;
                $totalDebitado = $sumDetalle->total_debitado;
                $totalCoseguro = $sumDetalle->total_coseguro;
            }
            $repoFactura->findByUpdateEstadoAndmontoFacturadoAndmontoAprobadoAndmontoDebitadoAndIdfacturaAndfechaLiquida(
                '1',
                $totalFacturado,
                $totalAprobado,
                $totalDebitado,
                $fechaActual,
                $request->id_factura,
            );

            return response()->json([
                'message' => 'Archivo importado correctamente'
            ], 200);
        } else {
            return response()->json([
                'message' => (!$request->hasFile('file') ? 'Seleccione un archivo a importar' : 'No se encontro la Factura de esta liquidación.')
            ], 409);
        }
    }
}
