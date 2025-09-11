<?php

namespace App\Http\Controllers\liquidaciones\repository;

use App\Models\liquidaciones\LiqDetalleMedicamentosEntity;

class LiqDetalleMedicamentosRepository
{

    public function save($params, $idLiq)
    {
        $detalle = LiqDetalleMedicamentosEntity::create([
            'id_liquidacion' => $idLiq,
            'id_medicamento' => $params['id_medicamento'],
            'cantidad' => $params['cantidad'],
            'precio_unitario' => $params['precio_unitario'],
            'monto_facturado' => $params['importe_facturado'],
            'cobertura_porcentaje' => $params['cobertura'],
            'cargo_os' => $params['acargo_os'],
            'debita_iva' => $params['debita_iva'],
            'id_tipo_motivo_debito' => $params['id_motivo_debito']
        ]);

        return $detalle;
    }

    public function saveId($id, $params)
    {
        $reg = LiqDetalleMedicamentosEntity::find($id);
        $reg->id_medicamento = $params['id_medicamento'];
        $reg->cantidad = $params['cantidad'];
        $reg->precio_unitario = $params['precio_unitario'];
        $reg->monto_facturado = $params['importe_facturado'];
        $reg->cobertura_porcentaje = $params['cobertura'];
        $reg->cargo_os = $params['acargo_os'];
        $reg->debita_iva = $params['debita_iva'];
        $reg->id_tipo_motivo_debito = $params['id_motivo_debito'];
        $reg->update();
    }

    public function deleteId($id)
    {
        return LiqDetalleMedicamentosEntity::find($id)->delete();
    }
}
