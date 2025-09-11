<?php

namespace App\Http\Controllers\facturacion;

use App\Http\Controllers\facturacion\repository\AuxiliaresRepository;
use App\Models\facturacion\TipoFacturacionEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuxiliaresFacturaController extends Controller
{
    public function getListaTipoFactura(AuxiliaresRepository $repository)
    {
        return response()->json($repository->findByListTipoFacturacion());
    }

    public function getTipoFacturaId(Request $request)
    {

        return response()->json(TipoFacturacionEntity::find($request->id));
    }

    public function getListaTipoComprobante(AuxiliaresRepository $repository)
    {
        return response()->json($repository->findByListTipoComprobantesFacturacion());
    }

    public function getListaTipoImputacionContable(AuxiliaresRepository $repository)
    {
        return response()->json($repository->findByListTipoImputacionContable());
    }

    public function getListaTipoIVA(AuxiliaresRepository $repository)
    {
        return response()->json($repository->findByListTipoTipoIva());
    }

    public function getListaTipoFiltro(AuxiliaresRepository $repository)
    {
        return response()->json($repository->findByListTipoFiltro());
    }

    public function getListaTipoEfector(AuxiliaresRepository $repository)
    {
        return response()->json($repository->findByListTipoEfector());
    }

    public function getListaTipoImputacioncontableSintetizada(AuxiliaresRepository $repository)
    {
        return response()->json($repository->findByListTipoImputacionContableSintetizada());
    }
}
