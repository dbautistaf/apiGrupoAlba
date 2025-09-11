<?php

namespace App\Http\Controllers\Facturacion\Services;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class   FacturasOspfController extends Controller
{
    public function getFacturasOspf(Request $request)
    {
        $data = [];
        if (!is_null($request->num_comprobante)) {
            $data = DB::select("SELECT * FROM vw_matriz_facturas_ospf WHERE numero = ? ORDER BY id_factura desc", [$request->num_comprobante]);
        } else {
            $data = DB::select("SELECT * FROM vw_matriz_facturas_ospf ORDER BY id_factura desc");
        }

        return response()->json($data);
    }

    public function getFacturasPendientesOspf(Request $request)
    {
        $data = [];
        if (!is_null($request->numero)) {
            $data = DB::select("SELECT * FROM vw_matriz_facturas_ospf WHERE estado = '0' AND numero LIKE ? ORDER BY id_factura desc", [$request->numero . '%']);
        } else {
            $data = DB::select("SELECT * FROM vw_matriz_facturas_ospf WHERE estado = '0' ORDER BY id_factura desc");
        }
        return response()->json($data);
    }
}
