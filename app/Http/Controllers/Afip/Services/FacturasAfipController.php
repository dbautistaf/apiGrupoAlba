<?php

namespace App\Http\Controllers\Afip\Services;

use App\Http\Controllers\Afip\Repository\FacturasAfipRepository;
use App\Imports\FacturasAfipImport;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;

class FacturasAfipController extends Controller
{

    protected $repoFacturasAfip;

    public function __construct(FacturasAfipRepository $repoFacturasAfip)
    {
        $this->repoFacturasAfip = $repoFacturasAfip;
    }

    public function getImportar(Request $request)
    {
        if ($request->hasFile('archivo')) {
            Excel::import(new FacturasAfipImport, $request->file('archivo'));
            return response()->json(["message" => "Archivo importado con éxito."]);
        } else {
            return response()->json(["message" => "No se encontro un archivo a importar"], 409);
        }
    }

    public function getListar(Request $request)
    {
        return response()->json($this->repoFacturasAfip->findByListTodos($request));
    }
}
