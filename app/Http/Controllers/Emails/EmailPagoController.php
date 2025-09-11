<?php

namespace App\Http\Controllers\Emails;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use App\Mail\PagoProveedorEmail;
use App\Models\Tesoreria\TesPagoEntity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailPagoController extends Controller
{
    public function sendEmailPagoProveedor(Request $request)
    {

        // Verificar si viene como parámetro en la URL o en el body
        $idPago = $request->id_pago ?? $request->input('id_pago') ?? $request->route('id_pago');


        if (!$idPago) {
            return response()->json(['message' => 'ID de pago no proporcionado'], 400);
        }

        // Primero verificar si existe el pago sin relaciones
        $pagoExiste = TesPagoEntity::where('id_pago', $idPago)->exists();
        \Log::info('Existe pago con ID ' . $idPago . ': ' . ($pagoExiste ? 'SI' : 'NO'));

        if (!$pagoExiste) {
            return response()->json(['message' => 'Pago no encontrado con ID: ' . $idPago], 404);
        }

        $query = TesPagoEntity::with([
            'estado',
            'opa.proveedor',
            'opa.proveedor.tipoIva',
            'opa.proveedor.datosBancarios',
            'opa.prestador',
            'opa.prestador.tipoIva',
            'opa.prestador.datosBancarios',
            'opa.factura',
            'opa.factura.detalle',
            'opa.factura.detalle.articulo',
            'formaPago',
            'cuenta',
            'cuenta.entidadBancaria'
        ])
            ->where('id_pago', $idPago)
            ->first();

        if (!$query) {
            \Log::error('Error al cargar relaciones para pago ID: ' . $idPago);
            return response()->json(['message' => 'Error al cargar datos del pago'], 500);
        }

        if (!$query->opa) {
            return response()->json(['message' => 'Orden de pago asociada no encontrada'], 404);
        }

        Carbon::setLocale('es');
        $fecha = Carbon::parse($query->fecha_confirma_pago ?? $query->fecha_registra);

        // Crear objeto razon_social con datos del .env
        $razon_social = (object) [
            'id_razon' => env('EMPRESA_ID_RAZON', 1),
            'razon_social' => env('EMPRESA_RAZON_SOCIAL', 'OBRA SOCIAL S.A.'),
            'iva' => env('EMPRESA_IVA', 'Responsable Inscripto'),
            'domicilio' => env('EMPRESA_DOMICILIO', 'Avenida'),
            'cuit' => env('EMPRESA_CUIT', '30-00000000-0')
        ];

        // Determinar si es proveedor o prestador basado en el tipo_factura
        $esProveedor = $query->tipo_factura === 'PROVEEDOR';
        $entidad = $esProveedor ? $query->opa->proveedor : $query->opa->prestador;

        if (!$entidad) {
            $tipoEntidad = $esProveedor ? 'proveedor' : 'prestador';
            return response()->json(['message' => ucfirst($tipoEntidad) . ' no encontrado'], 404);
        }

        $tipoComprobante = $esProveedor ? 'Proveedor' : 'Prestador';

        $datos = [
            "comprobante_nro" => $query->num_pago ?? 'PAGO-' . $query->id_pago,
            "fecha_emision" => $query->fecha_confirma_pago ?? $query->fecha_registra,
            "cuit_proveedor" => $entidad->cuit ?? 'N/A',
            "nombre_proveedor" => $entidad->razon_social ?? 'N/A',
            "iva_proveedor" => $entidad->tipoIva?->descripcion_iva ?? 'N/A',
            "domicilio_proveedor" => $entidad->direccion ?? 'N/A',
            "cbu_proveedor" => $entidad->datosBancarios?->cbu ?? 'N/A',
            "detalle" => $query->opa->factura?->detalle ?? [],
            "total" => $query->monto_pago ?? 0,
            "observaciones" => $query->observaciones ?? 'PAGO ' . strtoupper($tipoComprobante) . ' ' . strtoupper($fecha->translatedFormat('F')) . ' ' . $fecha->year,
            "razon_social" => $razon_social,
            "facturas" => $query->opa->factura ? [$query->opa->factura] : [],
            "pagos" => [$query],
            "pago_individual" => $query,
            "tipo_entidad" => $tipoComprobante,
            // Agregar datos seguros para la vista
            "forma_pago" => $query->formaPago?->tipo_pago ?? 'N/A',
            "cuenta_nombre" => $query->cuenta?->nombre_cuenta ?? 'N/A',
            "banco_descripcion" => $query->cuenta?->entidadBancaria?->descripcion_banco ?? 'N/A',
        ];

        $subjectText = 'Comprobante de Pago a ' . $tipoComprobante;

        Mail::to($request->email)->send(new PagoProveedorEmail($datos));

        return response()->json(['message' => 'Correo de pago a ' . strtolower($tipoComprobante) . ' enviado con éxito']);
    }
}
