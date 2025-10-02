<?php

namespace App\Http\Controllers\Emails;

use App\Http\Controllers\liquidaciones\repository\LiqDebitoInternoRepository;
use App\Http\Controllers\liquidaciones\repository\LiquidacionesFacturaRepository;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use App\Mail\EnviarPDFMail;
use App\Mail\OrdenPagoMail;
use App\Models\liquidaciones\LiqDebitoInternoEntity;
use App\Models\Tesoreria\TesOrdenPagoEntity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Tesoreria\Services\TesPagosController;
use App\Http\Controllers\Contabilidad\Repository\PeriodosContablesRepository;
use App\Http\Controllers\Contabilidad\Repository\ProveedorPlanesCuentaRepository;
use App\Http\Controllers\Contabilidad\Repository\FormaPagoCuentaContableRepository;
use App\Http\Controllers\Utils\CorrelativosOspfRepository;
use Illuminate\Support\Facades\App;

class EmailOpaController extends Controller
{
    public function sendEmailOpaProveedor(Request $request)
    {
        
        $query = TesOrdenPagoEntity::with(['estado', 'proveedor', 'factura', 'factura.detalle', 'factura.detalle.articulo', 'prestador', 'proveedor.tipoIva', 'prestador.tipoIva', 'pagos', 'pagos.formaPago', 'pagos.cuenta', 'pagos.cuenta.entidadBancaria'])
            ->where('id_orden_pago', $request->id_orden_pago)
            ->first();

        Carbon::setLocale('es');
        $fecha = Carbon::parse($query->fecha_emision);

        // Crear objeto razon_social con datos del .env
        $razon_social = (object) [
            'id_razon' => env('EMPRESA_ID_RAZON', 1),
            'razon_social' => env('EMPRESA_RAZON_SOCIAL', 'OBRA SOCIAL S.A.'),
            'iva' => env('EMPRESA_IVA', 'Responsable Inscripto'),
            'domicilio' => env('EMPRESA_DOMICILIO', 'Avenida'),
            'cuit' => env('EMPRESA_CUIT', '30-00000000-0')
        ];

        $datos = [
            "comprobante_nro" => $query->num_orden_pago,
            "fecha_emision" => $query->fecha_emision,
            "cuit_proveedor" => $query->proveedor ? $query->proveedor->cuit : $query->prestador->cuit,
            "nombre_proveedor" => $query->proveedor ? $query->proveedor->razon_social : $query->prestador->razon_social,
            "iva_proveedor" => $query->proveedor ? $query->proveedor->tipoIva->descripcion_iva : $query->prestador->tipoIva->descripcion_iva,
            "domicilio_proveedor" => $query->proveedor ? $query->proveedor->direccion : $query->prestador->direccion,
            "cbu_proveedor" => $query->proveedor ? $query->proveedor->datosBancarios?->cbu : $query->prestador->datosBancarios?->cbu,
            "detalle" => $query->factura?->detalle,
            "total" => $query->monto_orden_pago,
            "debito" => $query->factura->total_debitado_liquidacion,
            // "observaciones" => 'PRESTACION ' . strtoupper($fecha->translatedFormat('F')) . ' ' . $fecha->year,
            "razon_social" => $razon_social,
            "facturas" => $query->factura ? [$query->factura] : [],
            "pagos" => $query->pagos ?? [],
        ];

        // Resolver TesPagosController desde el contenedor de servicios
        $tesPagosController = App::make(TesPagosController::class);

        // Obtener el PDF usando los datos del comprobante
        $pdfRequest = new Request([
            'fecha_registra' => $request->comprobante['fecha_comprobante'],
            'nombre_archivo' => $request->comprobante['nombre_archivo']
        ]);
        $pdfResponse = $tesPagosController->getVerAdjunto(app('App\Http\Controllers\Utils\ManejadorDeArchivosUtils'), $pdfRequest);

        // Adjuntar el PDF al correo
        $email = new OrdenPagoMail($datos);
        if ($pdfResponse->getStatusCode() === 200) {
            $email->attachData($pdfResponse->getContent(), $request->comprobante['nombre_archivo'], [
                'mime' => 'application/pdf',
            ]);
        }

        Mail::to($request->email)->send($email);

        return response()->json(['message' => 'Correo enviado con éxito']);
    }
}