<?php

namespace App\Http\Controllers\Afip\Services;

use App\Http\Controllers\Afip\Repository\ComprobantesAfipCompraRepository;
use App\Imports\ComprobantesAfipCompraImport;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ComprobantesAfipCompraController extends Controller
{

    protected $repoComprobante;

    public function __construct(ComprobantesAfipCompraRepository $repoComprobante)
    {
        $this->repoComprobante = $repoComprobante;
    }


    public function getImportarComprobantes(Request $request)
    {
        if ($request->hasFile('archivo')) {
            Excel::import(new ComprobantesAfipCompraImport, $request->file('archivo'));
            return response()->json(["message" => "Archivo importado con éxito."]);
        } else {
            return response()->json(["message" => "No se encontro un archivo a importar"], 409);
        }
    }

    public function getListar(Request $request)
    {
        return response()->json($this->repoComprobante->findByListTodos($request));
    }
}
