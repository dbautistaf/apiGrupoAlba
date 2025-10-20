<?php

namespace App\Http\Controllers\Emails;

use App\Http\Controllers\liquidaciones\repository\LiqDebitoInternoRepository;
use App\Http\Controllers\liquidaciones\repository\LiqMedicamentosRepository;
use App\Http\Controllers\liquidaciones\repository\LiquidacionesFacturaRepository;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use App\Mail\EnviarPDFMail;
use App\Models\liquidaciones\LiqDebitoInternoEntity;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

class EmailLiquidacionesDebitosController extends Controller
{

    public function getEnviarDebitoProveedor(LiqMedicamentosRepository $factura, LiquidacionesFacturaRepository $repo, LiqDebitoInternoRepository $repoDebito, Request $request)
    {
        $data = json_decode($request->data);

        $datos = $factura->findByFacturaId($data->idfactura);

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
            $nombreBoleta = 'BOLETA_DEBITO_' . $datos->id_factura . '.pdf';
            $urlFileBoleta = 'public/liquidaciones/archivos_mails_debito/' . $nombreBoleta;

            Storage::put($urlFileBoleta, $mpdf->Output('', 'S'));

            $pdfPath = storage_path('app/' . $urlFileBoleta);

            if (!file_exists($pdfPath)) {
                return response()->json(['error' => 'La boleta del debito no éxiste'], 409);
            }
            $archivos = [
                [
                    'path' => storage_path('app/' . $urlFileBoleta),
                    'nombre' => 'Boleta_debito'
                ]
            ];

            if ($request->hasFile('dictamen_medico')) {
                $horaCarga = Carbon::now('America/Argentina/Buenos_Aires')->format('His');
                $fileStorage = $request->file('dictamen_medico');
                $dictamen_medico = 'ARCHIVO_ADJUNTO_DM_' . $data->idfactura . "_" . $horaCarga . "." . $fileStorage->extension();
                $destinationPath = "public/liquidaciones/debito_interno";
                Storage::putFileAs($destinationPath, $fileStorage, $dictamen_medico);
                $archivos[] = [
                    'path' => storage_path('app/public/liquidaciones/debito_interno/' . $dictamen_medico),
                    'nombre' => 'dictamen_medico'
                ];
                $params = new \stdClass();
                $params->id_factura = $data->idfactura;
                $params->observaciones = "";
                $params->tipo = "DM";
                $repoDebito->findBySave($params, $dictamen_medico);
            }

            $factura = $repo->findByIdFactura($data->idfactura);
            if ($data->incluir) {
                $documentacion = LiqDebitoInternoEntity::where('id_factura', $data->idfactura)
                    ->where('tipo', 'DI')
                    ->get();
                $i = 1;
                foreach ($documentacion as $val) {
                    $archivos[] = [
                        'path' => storage_path('app/public/liquidaciones/debito_interno/' . $val['nombre_archivo']),
                        'nombre' => 'archivo_respaldatorio_debito_' . $i
                    ];
                    $i++;
                }

                $dictamenMedico = LiqDebitoInternoEntity::where('id_factura', $data->idfactura)
                    ->where('tipo', 'DM')
                    ->orderByDesc('id_debito')
                    ->limit('1')
                    ->first();

                if (!is_null($dictamenMedico)) {
                    $archivos[] = [
                        'path' => storage_path('app/public/liquidaciones/debito_interno/' . $dictamenMedico->nombre_archivo),
                        'nombre' => 'archivo_respaldatorio_debito_' . $i
                    ];
                }
            }

            $destinatarios = array_map('trim', explode(',', $data->mailPrestador));

            if ($data->debito) {
                Mail::to($destinatarios)->cc('debitos@grupoalba.com.ar')->send(new EnviarPDFMail([$archivos[0]], $factura, $data->asunto, $data->observaciones));
            } else {
                Mail::to($destinatarios)->cc('debitos@grupoalba.com.ar')->send(new EnviarPDFMail($archivos, $factura, $data->asunto, $data->observaciones));
            }

            return response()->json([
                'success' => true,
                'message' => 'Email enviado correctamente'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Error al descargar el PDF'
            ], 409);
        }
    }
}
