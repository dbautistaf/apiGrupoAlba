<?php

namespace App\Http\Controllers\Tesoreria\Services;

use App\Http\Controllers\Tesoreria\Repository\TesPagoDetalleRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;

class TesPagoDetalleController extends Controller
{
    protected $repo;

    public function __construct(TesPagoDetalleRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getPagoDetalleById($id)
    {
        $detalle = $this->repo->findById($id);
        return response()->json($detalle, 200);
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'id_pago',
            'id_forma_pago',
            'monto',
            'id_cuenta_bancaria',
            'num_cheque',
            'id_chequera',
            'porcentaje_retencion',
            'monto_retencion',
            'fecha_acreditacion',
            'observaciones',
            'cod_usuario',
            'fecha_registra',
            'nro_transferencia',
            'fecha_transferencia'
        ]);

        $detalle = $this->repo->create($data);
        return response()->json($detalle, 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->only([
            'id_forma_pago',
            'monto',
            'id_cuenta_bancaria',
            'num_cheque',
            'id_chequera',
            'porcentaje_retencion',
            'monto_retencion',
            'fecha_acreditacion',
            'observaciones',
            'cod_usuario',
            'nro_transferencia',
            'fecha_transferencia'
        ]);

        $detalle = $this->repo->update($id, $data);
        return response()->json($detalle, 200);
    }

    public function destroy($id)
    {
        $this->repo->delete($id);
        return response()->json(['message' => 'Detalle eliminado'], 200);
    }

    public function generarPdfPagoDetalle(Request $request)
    {
        try {
            // Validar que se proporcione el ID del detalle de pago
            if (!$request->has('idDetalle')) {
                return response()->json(['message' => 'ID de detalle de pago requerido'], 400);
            }

            // Traer detalle de pago con todas las relaciones necesarias
            $detalleEntity = $this->repo->findById($request->idDetalle);
            if (is_null($detalleEntity)) {
                return response()->json(['message' => 'Detalle de pago no encontrado'], 404);
            }

            // Cargar las relaciones necesarias si no están cargadas
            $detalleEntity->load([
                'pago.opa.proveedor',
                'pago.opa.prestador',
                'pago.retenciones',
                'formaPago',
                'cuenta.entidadBancaria',
                'usuario'
            ]);

            // Cargar relaciones de retenciones (tipoRetencion y regla)
            if ($detalleEntity->pago && $detalleEntity->pago->retenciones && $detalleEntity->pago->retenciones->count() > 0) {
                $detalleEntity->pago->retenciones->load('tipoRetencion', 'regla');
            }

            // Cargar chequera solo si existe el id_chequera
            if (!empty($detalleEntity->id_chequera)) {
                $detalleEntity->load('chequera');
            }

            // Normalizar estructura de datos para evitar errores en la vista
            if (!isset($detalleEntity->formaPago) || is_null($detalleEntity->formaPago)) {
                $detalleEntity->formaPago = (object) ['tipo_pago' => 'No especificado'];
            }

            if (!isset($detalleEntity->cuenta) || is_null($detalleEntity->cuenta)) {
                $detalleEntity->cuenta = (object) ['nombre_cuenta' => '', 'numero_cuenta' => ''];
                $detalleEntity->cuenta->entidadBancaria = (object) ['descripcion_banco' => ''];
            } else {
                if (!isset($detalleEntity->cuenta->entidadBancaria) || is_null($detalleEntity->cuenta->entidadBancaria)) {
                    $detalleEntity->cuenta->entidadBancaria = (object) ['descripcion_banco' => ''];
                }
            }

            if (!isset($detalleEntity->pago) || is_null($detalleEntity->pago)) {
                $detalleEntity->pago = (object) ['num_pago' => '', 'fecha_pago' => null];
                $detalleEntity->pago->opa = (object) ['num_orden_pago' => ''];
            } else {
                if (!isset($detalleEntity->pago->opa) || is_null($detalleEntity->pago->opa)) {
                    $detalleEntity->pago->opa = (object) ['num_orden_pago' => ''];
                }
            }

            // Renderizar vista para el PDF (usaremos una vista similar a la de OPA)
            $html = View::make('reportes.pdfpagodetalle', ['detalle' => $detalleEntity])->render();

            $mpdf = new \Mpdf\Mpdf([
                'default_font' => 'quicksand',
                'format' => 'A4',
                'margin_top' => 5,
                'margin_bottom' => 5,
                'margin_left' => 6,
                'margin_right' => 6
            ]);

            $mpdf->fontdata['quicksand'] = [
                'R' => 'resources/fonts/Quicksand-Regular.ttf',
                'B' => 'resources/fonts/Quicksand-Bold.ttf',
                'I' => 'resources/fonts/Quicksand-Light.ttf',
                'BI' => 'resources/fonts/Quicksand-SemiBold.ttf'
            ];

            $mpdf->SetFooter('Página {PAGENO} de {nbpg}||Generado el: {DATE d-m-Y}');
            $mpdf->WriteHTML($html);
            $pdfOutput = $mpdf->Output('detalle-pago-' . $detalleEntity->id_pago_detalle . '.pdf', 'S');

            return response($pdfOutput, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="detalle-pago-' . $detalleEntity->id_pago_detalle . '.pdf"');

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al exportar detalle de pago: ' . $e->getMessage()], 500);
        }
    }
}
