<?php

namespace App\Http\Controllers\Discapacidad;

use App\Http\Controllers\Discapacidad\Repository\RendicionFondosRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class RendicionFondosTesoreriaController extends Controller
{

    public function postProcesar(RendicionFondosRepository $repo, Request $request)
    {
        if (is_null($request->id_discapacidad_tesoreria)) {
            $repo->save($request);
            return response()->json(["message" => "Datos registrado correctamente"]);
        } else {
            $repo->saveId($request);
            return response()->json(["message" => "Datos actualizados correctamente"]);
        }
    }

    public function getBuscarId(RendicionFondosRepository $repo, Request $request)
    {

        return response()->json($repo->findByIdDiscapacidad($request->id));
    }


    public function getfiltrarDataTesoreriafacturas(RendicionFondosRepository $repo, Request $request)
    {
        $data = [];
        $desde = str_replace('-', '', $request->periodo_desde);
        $hasta = str_replace('-', '', $request->periodo_hasta);
        $estado = $request->estado;
        $top = $request->top;

        if (
            !is_null($request->cuil_benef)
            && !is_null($request->cuil_prestador)
            && !is_null($request->num_factura)
            && is_null($request->cae_cai)
        ) {
            $data = $repo->findTopByListCuilAfiAndCuitPrestadorAndNumFactura($desde, $hasta, $request->cuil_benef, $top, $estado, $request->cuil_prestador,$request->num_factura);

        } else if (
            !is_null($request->cuil_benef)
            && !is_null($request->cuil_prestador)
            && is_null($request->num_factura)
            && is_null($request->cae_cai)
        ) {
            $data = $repo->findTopByListCuilAfiAndCuitPrestador($desde, $hasta, $request->cuil_benef, $top, $estado, $request->cuil_prestador);

        } else if (
            !is_null($request->cuil_benef)
            && is_null($request->cuil_prestador)
            && is_null($request->num_factura)
            && is_null($request->cae_cai)
        ) {
            $data = $repo->findTopByListCuilAfi($desde, $hasta, $request->cuil_benef, $top, $estado);
        } else if (
            is_null($request->cuil_benef)
            && !is_null($request->cuil_prestador)
            && is_null($request->num_factura)
            && is_null($request->cae_cai)
        ) {
            $data = $repo->findTopByListCuitPrest($desde, $hasta, $request->cuil_prestador, $top, $estado);
        } else if (
            is_null($request->cuil_benef)
            && is_null($request->cuil_prestador)
            && !is_null($request->num_factura)
            && is_null($request->cae_cai)
        ) {
            $data = $repo->findTopByListNumfact($desde, $hasta, $request->num_factura, $top, $estado);
        } else if (
            is_null($request->cuil_benef)
            && is_null($request->cuil_prestador)
            && is_null($request->num_factura)
            && !is_null($request->cae_cai)
        ) {
            $data = $repo->findTopByListNumCaeCai($desde, $hasta, $request->cae_cai, $top, $estado);
        } else {
            $data = $repo->findTopByListperiodo($desde, $hasta, $top, $estado);
        }

        return response()->json($data);
    }

    public function getObtenerCantidadRegistros(RendicionFondosRepository $repo, Request $request)
    {
        $desde = str_replace('-', '', $request->periodo_desde);
        $hasta = str_replace('-', '', $request->periodo_hasta);
        $cantidad = $repo->findByCountRegistrosCargados($desde, $hasta);
        return response()->json(["cantidad" => $cantidad]);
    }
}
