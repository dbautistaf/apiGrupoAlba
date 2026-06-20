<?php

namespace App\Http\Controllers\mantenimiento;

use App\Models\DetallePrestacionesPracticaLaboratorioEntity;
use App\Models\PrestacionesPracticaLaboratorioEntity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPJasper\PHPJasper;

class PrestacionesController extends Controller
{
    public function getBuscarPrestacionesDNI(Request $request)
    {
        $data = PrestacionesPracticaLaboratorioEntity::with(['detalle', 'estadoPrestacion', 'afiliado', 'usuario', 'prestador', 'profesional'])
            ->where('dni_afiliado', $request->dni)
            ->orderByDesc('cod_prestacion')
            ->get();

        foreach ($data as $objeto) {
            $objeto->setAttribute('show', false);
        }

        return response()->json($data, 200);
    }

    public function getImprimirReporte(Request $request)
    {
        $input = storage_path('app/public/reports/rpt_prestacion.jrxml');
        $output = storage_path('app/public/reports');
        $ruta = storage_path('app/public/');
        $options = [
            'format' => ['pdf'],
            'locale' => 'en',
            'params' => ['P_RUTA' => $ruta, 'P_ID' => $request->prestacion],
            'db_connection' => [
                'driver' => 'mysql',
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
                'host' => env('DB_HOST'),
                'database' => env('DB_DATABASE'),
                'port' => env('DB_PORT')
            ]
        ];

        $jasper = new PHPJasper;

        $jasper->process(
            $input,
            $output,
            $options
        )->execute();

        return response()->file(storage_path('app/public/reports/rpt_prestacion.pdf'), ['Content-Type' => 'application/pdf']);
    }

    public function getImprimirReporteRN(Request $request)
    {
        $id = $request->prestacion;

        $prestacion = \App\Models\Internaciones\AutorizacionDatosRNEntity::with([
            'detalle.practica',
            'estadoPrestacion',
            'usuario',
            'prestador',
            'profesional',
            'recien_nacido.internacion.afiliado.obrasocial',
            'recien_nacido.internacion.afiliado.localidad',
            'recien_nacido.internacion.afiliado.tipoParentesco',
            'obraSocial',
            'tipoTramite'
        ])->find($id);

        if (!$prestacion) {
            return response()->json(['message' => 'Autorización de recién nacido no encontrada'], 404);
        }

        $is_rn = true;
        $centro_operativo = 'Buenos Aires';

        $afiliado = $prestacion->recien_nacido?->internacion?->afiliado;
        $beneficiario_nombre = $afiliado ? ($afiliado->nombre . ' ' . $afiliado->apellidos) : '';
        $beneficiario_nro = $afiliado ? $afiliado->cuil_benef : '';
        $beneficiario_dni = $afiliado ? $afiliado->dni : '';
        $beneficiario_tipo = $afiliado && $afiliado->tipoParentesco ? $afiliado->tipoParentesco->parentesco : 'TITULAR';
        $beneficiario_edad = $afiliado && $afiliado->fe_nac ? \Carbon\Carbon::parse($afiliado->fe_nac)->age : '';
        $beneficiario_localidad = $afiliado && $afiliado->localidad ? $afiliado->localidad->localidad : ($afiliado ? $afiliado->calle : '');
        $beneficiario_obrasocial = $prestacion->obraSocial?->locatorio ?? ($afiliado && $afiliado->obrasocial ? $afiliado->obrasocial->locatorio : 'BENE SALUD');

        $rn_nombre = $prestacion->recien_nacido ? ($prestacion->recien_nacido->nombre_rn . ' ' . $prestacion->recien_nacido->apellidos_rn) : '';
        $rn_dni = $prestacion->recien_nacido ? $prestacion->recien_nacido->dni_rn : '';
        $rn_fecha_nac = $prestacion->recien_nacido && $prestacion->recien_nacido->fecha_nacimiento
            ? \Carbon\Carbon::parse($prestacion->recien_nacido->fecha_nacimiento)->format('d/m/Y')
            : '';

        $tipo_tramite = $prestacion->tipoTramite?->descripcion ?? 'AUTORIZACIÓN RECIÉN NACIDO';
        $tramite_nro = $prestacion->cod_prestacion_rn;

        $fecha = $prestacion->fecha_registra ? \Carbon\Carbon::parse($prestacion->fecha_registra)->format('d/m/Y') : date('d/m/Y');

        $prestador_nombre = $prestacion->prestador?->razon_social ?? $prestacion->prestador?->nombre_fantasia ?? 'CENTRO MÉDICO';
        $prestador_direccion = $prestacion->domicilio_prestador ?? ($prestacion->prestador ? ($prestacion->prestador->calle . ' ' . $prestacion->prestador->numero) : 'S/D');
        $prestador_telefono = $prestacion->prestador?->telefono ?? 'null';

        $practicas = [];
        if ($prestacion->detalle) {
            foreach ($prestacion->detalle as $row) {
                $practicas[] = [
                    'nombre' => $row->practica?->nombre_practica ?? 'PRÁCTICA GENERAL',
                    'codigo' => $row->practica?->codigo_practica ?? '',
                    'cantidad' => $row->cantidad_autorizada ?? $row->cantidad_solicitada ?? 1
                ];
            }
        }

        $diagnostico = $prestacion->diagnostico ?? 'S/D';
        $estado_desc = $prestacion->estadoPrestacion?->descripcion ?? 'PENDIENTE';

        // Status formatting
        $estado_tramite = 'PENDIENTE';
        $estado_class = 'status-pending';
        $stamp_text = 'AUTORIZACIÓN PENDIENTE';
        $stamp_class = 'stamp-box-pending';

        if (in_array(strtoupper($estado_desc), ['AUTORIZADA', 'AUTORIZADO'])) {
            $estado_tramite = 'AUTORIZADO';
            $estado_class = 'status-authorized';
            $stamp_text = 'AUTORIZADO';
            $stamp_class = '';
        } elseif (in_array(strtoupper($estado_desc), ['NO AUTORIZADA', 'RECHAZADO', 'ELIMINADO'])) {
            $estado_tramite = 'NO AUTORIZADO';
            $estado_class = 'status-rejected';
            $stamp_text = 'TRÁMITE NO AUTORIZADO';
            $stamp_class = 'stamp-box-rejected';
        }

        $fecha_autorizacion = $prestacion->fecha_impresion
            ? \Carbon\Carbon::parse($prestacion->fecha_impresion)->format('d/m/Y')
            : ($prestacion->fecha_modifica
                ? \Carbon\Carbon::parse($prestacion->fecha_modifica)->format('d/m/Y')
                : $fecha);

        $observaciones = $prestacion->observaciones;
        $operador_nombre = $prestacion->usuario?->nombre_apellidos ?? 'Auditor';

        // Render Blade View
        $html = \Illuminate\Support\Facades\View::make('reportes.pdfprestacion', [
            'is_rn' => $is_rn,
            'centro_operativo' => $centro_operativo,
            'tipo_tramite' => $tipo_tramite,
            'tramite_nro' => $tramite_nro,
            'fecha' => $fecha,
            'beneficiario_nombre' => $beneficiario_nombre,
            'beneficiario_nro' => $beneficiario_nro,
            'beneficiario_dni' => $beneficiario_dni,
            'beneficiario_tipo' => $beneficiario_tipo,
            'beneficiario_edad' => $beneficiario_edad,
            'beneficiario_localidad' => $beneficiario_localidad,
            'beneficiario_obrasocial' => $beneficiario_obrasocial,
            'rn_nombre' => $rn_nombre,
            'rn_dni' => $rn_dni,
            'rn_fecha_nac' => $rn_fecha_nac,
            'prestador_nombre' => $prestador_nombre,
            'prestador_direccion' => $prestador_direccion,
            'prestador_telefono' => $prestador_telefono,
            'practicas' => $practicas,
            'diagnostico' => $diagnostico,
            'estado_tramite' => 'AUTORIZADO',
            'estado_class' => $estado_class,
            'fecha_autorizacion' => $fecha_autorizacion,
            'observaciones' => $observaciones,
            'stamp_text' => 'AUTORIZADO',
            'stamp_class' => $stamp_class,
            'operador_nombre' => $operador_nombre,
            'id_locatario' => $prestacion->id_locatorio
        ])->render();

        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'helvetica',
            'format' => 'A4',
            'margin_top' => 5,
            'margin_bottom' => 5,
            'margin_left' => 6,
            'margin_right' => 6
        ]);

        if (file_exists(base_path('resources/fonts/Quicksand-Regular.ttf'))) {
            $mpdf->fontdata['centurygothic'] = [
                'R' => 'resources/fonts/Quicksand-Regular.ttf',
                'B' => 'resources/fonts/Quicksand-Bold.ttf',
                'I' => 'resources/fonts/Quicksand-Light.ttf',
                'BI' => 'resources/fonts/Quicksand-SemiBold.ttf'
            ];
            $mpdf->default_font = 'centurygothic';
        }

        $mpdf->WriteHTML($html);

        $pdfOutput = $mpdf->Output('prestacion-rn-' . $tramite_nro . '.pdf', 'S');

        return response($pdfOutput, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="prestacion-rn-' . $tramite_nro . '.pdf"');
    }
}
