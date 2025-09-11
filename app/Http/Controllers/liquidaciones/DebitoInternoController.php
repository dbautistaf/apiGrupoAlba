<?php

namespace App\Http\Controllers\liquidaciones;

use App\Http\Controllers\liquidaciones\repository\LiqDebitoInternoRepository;
use App\Http\Controllers\liquidaciones\repository\LiqMedicamentosRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

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
            $pdf = Pdf::loadView('rpt_debito_liquidacion', ["factura" => $datos, "detalle" => $detalle]);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download('carnet.pdf');
        } else {
            return response()->json(['error' => 'La factura no existe'], 404);
        }
    }
}
