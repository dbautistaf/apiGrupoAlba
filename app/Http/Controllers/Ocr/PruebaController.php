<?php
namespace App\Http\Controllers\Ocr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToText\Pdf;
use thiagoalessio\TesseractOCR\TesseractOCR;
class PruebaController extends Controller
{

    public function getLeerArchivo(Request $request)
    {
        $ocr = new TesseractOCR("/public/storage/archivos-importar/ocr.pdf");
    $text = $ocr->run();

        return response()->json(["message" => $text]);
    }
}
