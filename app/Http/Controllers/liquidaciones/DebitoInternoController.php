<?php

namespace App\Http\Controllers\liquidaciones;

use App\Http\Controllers\liquidaciones\repository\LiqDebitoInternoRepository;
use App\Http\Controllers\liquidaciones\repository\LiqMedicamentosRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Mpdf\Mpdf;

class DebitoInternoController extends Controller
{

    public function postAltaDocumento(LiqDebitoInternoRepository $repo, Request $request)
    {
        $data = json_decode($request->data);
        $name_file = $repo->findByUploadFile($request, $data->tipo);
        $repo->findBySave($data, $name_file);
        return response()->json(["message" => "Archivo cargado correctamente."]);
    }

    public function getListarDebitosInternos(LiqDebitoInternoRepository $repo, Request $request)
    {
        return response()->json($repo->findByListIdFactura($request->id_factura, $request->tipo));
    }

    public function deleteDebitoInterno(LiqDebitoInternoRepository $repo, Request $request)
    {
        $repo->findByDeleteId($request->id);
        return response()->json(["message" => "Registro eliminado correctamente"]);
    }

    public function getViewFile(Request $request)
    {
        $path = 'public/liquidaciones/debito_interno/' . $request->file;

        if (!Storage::exists($path)) {
            return response()->json(['error' => 'File not found.'], Response::HTTP_NOT_FOUND);
        }

        $fileContent = Storage::get($path);
        $fileMimeType = Storage::mimeType($path);

        return response($fileContent, 200)
            ->header('Content-Type', $fileMimeType);
    }

    public function getDownloadDebitoLiquidacion(LiqMedicamentosRepository $factura, Request $request)
    {
        $datos = $factura->findByFacturaId($request->receta);

        if ($datos) {
            $detalle = $factura->findByDetalleFacturaId($datos->id_factura, $datos->tipo_detalle);
            ini_set('memory_limit', '1024M');
            ini_set('pcre.backtrack_limit', '10000000');
            ini_set('pcre.recursion_limit', '10000000');

            $mpdf = new Mpdf(['format' => 'A4-L']);

            // Escribimos encabezado
            $mpdf->WriteHTML(view('debito.rpt_debito_liquidacion_header', ['factura' => $datos])->render());

            // Dividimos el detalle en partes
            foreach (array_chunk($detalle, 2000) as $chunk) {
                $htmlChunk = view('debito.rpt_debito_liquidacion_detalle', ['detalle' => $chunk])->render();
                $mpdf->WriteHTML($htmlChunk);
            }

            // Pie de página
            $html = view('debito.rpt_debito_liquidacion_footer', ['factura' => $datos])->render();
            $mpdf->WriteHTML($html);

            // Generar y guardar
            //$nombreBoleta = 'BOLETA_DEBITO_' . $datos->id_factura . '.pdf';
            //$urlFileBoleta = 'public/liquidaciones/archivos_mails_debito/' . $nombreBoleta;
            return response($mpdf->Output('debito.pdf', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="medicacion.pdf"');
        } else {
            return response()->json(['error' => 'La factura no existe'], 404);
        }
    }
}
