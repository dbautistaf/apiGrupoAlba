<?php

namespace App\Http\Controllers\Emails;

use App\Http\Controllers\facturacion\repository\FacturaRepository;
use Illuminate\Routing\Controller;
use App\Mail\FacturacionMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailFacturacionController extends Controller
{

    public function sendEmailFacturacion(Request $request, FacturaRepository $facturaRepository)
    {
        $factura = $facturaRepository->findByIdFactura($request->id);

        $neto = 0.0;
        $impuestos = 0.0;

        foreach ($factura->detalle as $item) {
            $neto += $item->subtotal;
        }

        foreach ($factura->impuesto as $item) {
            $impuestos += $item->importe;
        }

        $datos = [
            "comprobante_nro" => $factura->numero,
            "tipoComprobante" => $factura->tipoComprobante->descripcion,
            "tipo_letra" => $factura->tipo_letra,
            "fecha_emision" => $factura->fecha_comprobante,
            "fecha_vencimiento" => $factura->fecha_vencimiento,
            "cuit_proveedor" => $factura->proveedor ? $factura->proveedor->cuit : $factura->prestador->cuit,
            "nombre_proveedor" => $factura->proveedor ? $factura->proveedor->razon_social : $factura->prestador->razon_social,
            "iva_proveedor" => $factura->proveedor ? $factura->proveedor->tipoIva->descripcion_iva : $factura->prestador->tipoIva->descripcion_iva,
            "periodo" => $factura->periodo,
            "tipo" => $factura->tipoFactura->descripcion,
            "sucursal" => $factura->sucursal,
            "numero" => $factura->numero,
            "detalle" => $factura->detalle,
            "impuesto" => $factura->impuesto,
            "neto" => $neto,
            "impuestos" => $impuestos,
            "descuentos" => 0.00,
            "total" => $neto + $impuestos
        ];

        Mail::to($request->email)->send(new FacturacionMail($datos));

        return response()->json(['message' => 'Correo enviado con éxito']);
    }
}
