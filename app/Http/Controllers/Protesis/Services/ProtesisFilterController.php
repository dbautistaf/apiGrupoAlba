<?php

namespace App\Http\Controllers\Protesis\Services;

use App\Http\Controllers\Protesis\Repository\ProtesisFiltrosRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProtesisFilterController extends Controller
{
    public function getFiltrar(ProtesisFiltrosRepository $repoFiltro, Request $request)
    {
        $data = [];

        $desde = $request->desde_solicitud;
        $hasta = $request->hasta_solicitud;
        $num_pedido = $request->num_pedido;
        $num_autorizacion = $request->num_autorizacion;
        $origen = $request->origen;
        $afiliado = $request->afiliado;
        $estado = $request->estado;
        $desde_entrega = $request->desde_entrega;
        $hasta_entrega = $request->hasta_entrega;


        if (
            !is_null($num_pedido)
            && is_null($num_autorizacion)
            && is_null($origen)
            && is_null($afiliado)
            && is_null($estado)
            && is_null($desde_entrega)
            && is_null($hasta_entrega)
        ) {
            $data = $repoFiltro->findByListFechaSolicitaBetweenAndNumPedido($desde, $hasta, $num_pedido, 100);
        } else if (
            is_null($num_pedido)
            && !is_null($num_autorizacion)
            && is_null($origen)
            && is_null($afiliado)
            && is_null($estado)
            && is_null($desde_entrega)
            && is_null($hasta_entrega)
        ) {
            $data = $repoFiltro->findByListFechaSolicitaBetweenAndNumAutoriza($desde, $hasta, $num_autorizacion, 100);
        } else if (
            is_null($num_pedido)
            && is_null($num_autorizacion)
            && !is_null($origen)
            && is_null($afiliado)
            && is_null($estado)
            && is_null($desde_entrega)
            && is_null($hasta_entrega)
        ) {
            $data = $repoFiltro->findByListFechaSolicitaBetweenAndLocatario($desde, $hasta, $origen, 100);
        } else if (
            is_null($num_pedido)
            && is_null($num_autorizacion)
            && is_null($origen)
            && !is_null($afiliado)
            && is_null($estado)
            && is_null($desde_entrega)
            && is_null($hasta_entrega)
        ) {
            if (is_numeric($afiliado)) {
                $data = $repoFiltro->findByListFechaSolicitaBetweenAndDniAfi($desde, $hasta, $afiliado, 100);
            } else {
                $data = $repoFiltro->findByListFechaSolicitaBetweenAndAfiliado($desde, $hasta, $afiliado, 100);
            }
        } else if (
            is_null($num_pedido)
            && is_null($num_autorizacion)
            && is_null($origen)
            && is_null($afiliado)
            && !is_null($estado)
            && is_null($desde_entrega)
            && is_null($hasta_entrega)
        ) {
            $data = $repoFiltro->findByListFechaSolicitaBetweenAndEstado($desde, $hasta, $estado, 100);
        } else if (
            is_null($num_pedido)
            && is_null($num_autorizacion)
            && is_null($origen)
            && !is_null($afiliado)
            && !is_null($estado)
            && is_null($desde_entrega)
            && is_null($hasta_entrega)
        ) {
            if (is_numeric($afiliado)) {
                $data = $repoFiltro->findByListFechaSolicitaBetweenAndDniAfiAndEstado($desde, $hasta, $afiliado, $estado, 100);
            } else {
                $data = $repoFiltro->findByListFechaSolicitaBetweenAndAfiliadoAndEstado($desde, $hasta, $afiliado, $estado, 100);
            }
        } else {
            $data = $repoFiltro->findByListFechaSolicitaBetweenAndLimit($desde, $hasta, 100);
        }


        return response()->json($data);
    }

    public function getListarProtesisAfiliado(ProtesisFiltrosRepository $repoFiltro, Request $request)
    {
        return response()->json($repoFiltro->findByListDni($request->dni));
    }
}
