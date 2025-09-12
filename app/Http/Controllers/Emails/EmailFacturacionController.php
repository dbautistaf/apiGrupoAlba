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
        try {
            // Validar datos requeridos
            $request->validate([
                'id' => 'required|integer',
                'email' => 'required|email'
            ]);

            $factura = $facturaRepository->findByIdFactura($request->id);

            if (!$factura) {
                return response()->json(['error' => 'Factura no encontrada'], 404);
            }

            $neto = 0.0;
            $impuestos = 0.0;

            // Calcular neto
            foreach ($factura->detalle as $item) {
                $neto += $item->subtotal ?? 0;
            }

            // Calcular impuestos
            foreach ($factura->impuesto as $item) {
                $impuestos += $item->importe ?? 0;
            }

            $datos = [
                "comprobante_nro" => $factura->numero ?? '',
                "tipoComprobante" => $factura->tipoComprobante->descripcion ?? '',
                "tipo_letra" => $factura->tipo_letra ?? '',
                "fecha_emision" => $factura->fecha_comprobante ?? '',
                "fecha_vencimiento" => $factura->fecha_vencimiento ?? '',
                "cuit_proveedor" => $factura->proveedor ? $factura->proveedor->cuit : ($factura->prestador->cuit ?? ''),
                "nombre_proveedor" => $factura->proveedor ? $factura->proveedor->razon_social : ($factura->prestador->razon_social ?? ''),
                "iva_proveedor" => $factura->proveedor ? ($factura->proveedor->tipoIva->descripcion_iva ?? '') : ($factura->prestador->tipoIva->descripcion_iva ?? ''),
                "periodo" => $factura->periodo ?? '',
                "tipo" => $factura->tipoFactura->descripcion ?? '',
                "sucursal" => $factura->sucursal ?? '',
                "numero" => $factura->numero ?? '',
                "detalle" => $factura->detalle ?? collect(),
                "impuesto" => $factura->impuesto ?? collect(),
                "neto" => $neto,
                "impuestos" => $impuestos,
                "descuentos" => $factura->descuentos->sum('importe') ?? 0.00,
                "total" => ($neto + $impuestos) - ($factura->descuentos->sum('importe') ?? 0.00),
                // Datos faltantes para el template
                "locatario" => $factura->cod_sindicato ?? 1,
                "razon_social" => $factura->razonSocial ?? (object) [
                    'razon_social' => '',
                    'iva' => '',
                    'domicilio' => '',
                    'cuit' => ''
                ],
                "codigo_opa" => 'OPA-' . str_pad($factura->id_factura ?? 0, 8, '0', STR_PAD_LEFT),
                "fecha_confirma_pago" => $factura->opa && $factura->opa->fechapagos && $factura->opa->fechapagos->first()
                    ? $factura->opa->fechapagos->first()->fecha_confirma
                    : '',
            ];

            Mail::to($request->email)->send(new FacturacionMail($datos));

            return response()->json([
                'message' => 'Correo enviado con éxito',
                'factura_numero' => $factura->numero,
                'email' => $request->email
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al enviar el correo: ' . $e->getMessage()], 500);
        }
    }


}