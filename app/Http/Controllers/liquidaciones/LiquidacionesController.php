<?php

namespace App\Http\Controllers\liquidaciones;

use App\Http\Controllers\facturacion\repository\FacturasPrestadoresRepository;
use App\Http\Controllers\liquidaciones\repository\LiqMedicamentosRepository;
use App\Http\Controllers\liquidaciones\repository\LiquidacionesDetalleRepository;
use App\Http\Controllers\liquidaciones\repository\LiquidacionesRepository;
use App\Http\Controllers\Tesoreria\Repository\TestOrdenPagoRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LiquidacionesController extends Controller
{
    public function postProcesarLiquidacion(
        LiquidacionesRepository $repo,
        LiquidacionesDetalleRepository $detalleRepo,
        FacturasPrestadoresRepository $repoFactura,
        TestOrdenPagoRepository $opa,
        Request $request
    ) {
        DB::beginTransaction();
        try {
            $liquidacion = null;
            $totalFacturado = 0;
            $totalAprobado = 0;
            $totalDebitado = 0;
            $totalCoseguro = 0;
            $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
            $mensaje = "";
            if (is_null($request->id_liquidacion)) {
                $liquidacion = $repo->findBySave($request);

                foreach ($request->detalle as $value) {
                    $detalleRepo->findBySave($value, $liquidacion->id_liquidacion);
                    /*  $totalFacturado += $value['monto_facturado'];
                     $totalAprobado += $value['monto_aprobado'];
                     $totalDebitado += $value['monto_debitado'];
                      */
                    $totalCoseguro += $value['coseguro'];
                }
                $liquidacion->total_coseguro = $totalCoseguro;
                $liquidacion->update();
                $sumDetalle = $repo->fidByObtenerTotalDebitadoFactura($request->id_factura);
                if (!is_null($sumDetalle)) {
                    $totalFacturado = $sumDetalle->total_facturado;
                    $totalAprobado = $sumDetalle->total_aprobado;
                    $totalDebitado = $sumDetalle->total_debitado;
                    $totalCoseguro = $sumDetalle->total_coseguro;
                }


                $repoFactura->findByUpdateEstadoAndmontoFacturadoAndmontoAprobadoAndmontoDebitadoAndIdfacturaAndfechaLiquida(
                    '1',
                    $totalFacturado,
                    $totalAprobado,
                    $totalDebitado,
                    $fechaActual,
                    $liquidacion->id_factura,
                );

                $mensaje = "Registro procesado correctamente.";
            } else {
                /*
                if (!$opa->findByExistsOpaFacturaEstado($request->factura, 1)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'La Factura ya tiene un OPA en proceso y no puede ser modificada'
                    ], 409);
                } */

                $liquidacion = $repo->findByUpdateLiquidacion($request);
                foreach ($request->detalle as $value) {
                    $detalleRepo->findByUpdateDetalleId($value);
                    /* $totalFacturado  += $value['monto_facturado'];
                    $totalAprobado  += $value['monto_aprobado'];
                    $totalDebitado  += $value['monto_debitado'];
                     */
                    $totalCoseguro += $value['coseguro'];
                }
                $detalleRepo->findByUpdateMontosCabecera($liquidacion->id_liquidacion);

                $liquidacion->total_coseguro = $totalCoseguro;
                $liquidacion->update();
                $sumDetalle = $repo->fidByObtenerTotalDebitadoFactura($request->id_factura);
                if (!is_null($sumDetalle)) {
                    $totalFacturado = $sumDetalle->total_facturado;
                    $totalAprobado = $sumDetalle->total_aprobado;
                    $totalDebitado = $sumDetalle->total_debitado;
                    $totalCoseguro = $sumDetalle->total_coseguro;

                    /* $liquidacion->total_debitado = $totalDebitado;
                    $liquidacion->total_aprobado = $totalAprobado;
                    $liquidacion->total_coseguro = $totalCoseguro; */
                }


                $repoFactura->findByUpdateEstadoAndmontoFacturadoAndmontoAprobadoAndmontoDebitadoAndIdfactura(
                    '1',
                    $totalFacturado,
                    $totalAprobado,
                    $totalDebitado,
                    $liquidacion->id_factura,
                );
                $mensaje = "Registro actualizado correctamente.";
            }

            DB::commit();
            return response()->json(["message" => $mensaje]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function postUpdateLinea(LiquidacionesDetalleRepository $repo, Request $request)
    {

        $repo->findByUpdateLinea($request);
        $repo->findByUpdateMontosCabecera($request->id_liquidacion);

        return response()->json(['message' => 'Linea actualizada correctamente']);
    }

    public function getEliminarLiquidacionConDetalle(
        LiquidacionesDetalleRepository $repo,
        LiqMedicamentosRepository $repoLiqMed,
        Request $request
    ) {
        if ($request->tipo === 'Practica') {
            $repo->findByLiquidacionAndDetalleId($request->id);
        } else {
            $repoLiqMed->findByLiquidacionAndDetalleId($request->id);
        }


        return response()->json(['message' => 'Registros eliminados correctamente']);
    }

    public function getEliminarMasivoLiquidacionConDetalle(
        LiquidacionesDetalleRepository $repo,
        LiqMedicamentosRepository $repoLiqMed,
        Request $request
    ) {
        $detalle = $request->input('detalle');
        $tipo = $request->input('tipo');

        if ($tipo === 'Practica') {
            foreach ($detalle as $item) {
                $repo->findByLiquidacionAndDetalleId($item);
            }
        } else {
            foreach ($detalle as $item) {
                $repoLiqMed->findByLiquidacionAndDetalleId($item);
            }
        }

        return response()->json(['message' => 'Registros eliminados correctamente']);
    }

    public function getLiquidaciones(LiquidacionesRepository $repo, Request $request)
    {
        $data = [];

        if (
            !is_null($request->id_locatorio)
            && is_null($request->cuil_afiliado)
            && is_null($request->nombre_afiliado)
        ) {
        } else {
            $data = $repo->findByFechaPrestacionBetweenAndTipodetalle($request->prestacion_desde, $request->prestacion_hasta, $request->id_factura, $request->tipo_detalle);
        }

        return response()->json($data);
    }

    public function getLiquidacionesDetallado(Request $request)
    {
        $data = DB::select("SELECT
                                ld.id_detalle,
                                l.id_liquidacion,
                                ld.fecha_prestacion AS detalle,
                                loc.locatorio AS origen,
                                afi.cuil_benef AS cuil_afiliado,
                                CONCAT(afi.apellidos, ' ', afi.nombre) AS afiliado,
                                'Práctica' AS tipo,
                                '2 - Global' AS ug,
                                ld.monto_facturado AS facturado,
                                ld.monto_aprobado AS aprobado,
                                ld.monto_debitado AS debitado,
                                ld.debita_iva AS debita_iva,
                                ld.debita_coseguro AS debita_coseguro,
                                ld.coseguro AS total_coseguro,
                                usu.nombre_apellidos AS usuario,
                                ld.estado AS estado,
                                ld.costo_practica,
                                pr.nombre_practica
                            FROM tb_liquidaciones_detalle ld
                            JOIN tb_liquidaciones l ON ld.id_liquidacion = l.id_liquidacion
                            JOIN tb_facturacion_datos fa ON l.id_factura = fa.id_factura
                            JOIN tb_padron afi ON l.id_afiliado = afi.id
                            JOIN tb_practicas_matriz pr ON ld.id_identificador_practica = pr.id_identificador_practica
                            LEFT JOIN tb_locatorio loc ON afi.id_locatario = loc.id_locatorio
                            LEFT JOIN tb_usuarios usu ON l.cod_usuario = usu.cod_usuario
                            where fa.id_factura = ?", [$request->id_factura]);

        return response()->json($data);
    }

    public function getDatosEditarliquidacionPracticaDetallado(Request $request)
    {
        $cabecera = DB::select("SELECT
                                l.id_liquidacion AS id_liquidacion,
                                fa.id_factura AS id_factura,
                                l.id_afiliado AS id_afiliado,
                                CONCAT(afi.apellidos, ' ', afi.nombre) AS afiliado,
                                l.edad_afiliado AS edad_afiliado,
                                l.id_cobertura AS id_cobertura,
                                l.id_tipo_iva AS id_tipo_iva,
                                afi.dni AS dni_afiliado,
                                prf.dni AS dni_medico,
                                prf.apellidos_nombres AS medico,
                                l.cod_profesional AS cod_profesional,
                                l.cod_provincia AS cod_provincia,
                                l.diagnostico AS diagnostico,
                                l.observaciones AS observaciones,
                                l.num_lote AS num_lote
                            FROM
                                tb_liquidaciones l
                                JOIN tb_padron afi ON l.id_afiliado = afi.id
                                LEFT JOIN tb_locatorio loc ON afi.id_locatario = loc.id_locatorio
                                LEFT JOIN tb_usuarios usu ON usu.cod_usuario = l.cod_usuario
                                LEFT JOIN tb_facturacion_datos fa ON l.id_factura = fa.id_factura
                                LEFT JOIN tb_profesionales_prestador prf ON l.cod_profesional = prf.cod_profesional
                                JOIN tb_liquidaciones_detalle dt ON l.id_liquidacion = dt.id_liquidacion
                            WHERE
                                dt.id_detalle = ?;
                                ", [$request->id]);

        $detalle = DB::select("SELECT
                                dt.fecha_prestacion AS fecha_prestacion,
                                dt.id_identificador_practica AS id_identificador_practica,
                                pr.codigo_practica AS codigo_practica,
                                TRIM(pr.nombre_practica) AS practica,
                                dt.costo_practica AS costo_practica,
                                dt.cantidad AS cantidad,
                                dt.porcentaje_hon AS porcentaje_hon,
                                dt.porcentaje_gast AS porcentaje_gast,
                                dt.monto_facturado AS monto_facturado,
                                dt.monto_aprobado AS monto_aprobado,
                                dt.monto_debitado AS monto_debitado,
                                dt.coseguro AS coseguro,
                                dt.debita_coseguro AS debita_coseguro,
                                dt.debita_iva AS debita_iva,
                                dt.id_tipo_motivo_debito AS id_tipo_motivo_debito,
                                mb.descripcion_motivo AS motivo_debito,
                                dt.observacion_debito AS observacion_debito,
                                dt.id_detalle AS id_detalle
                            FROM
                                tb_liquidaciones_detalle dt
                                JOIN tb_practicas_matriz pr ON dt.id_identificador_practica = pr.id_identificador_practica
                                JOIN tb_liquidaciones lq ON dt.id_liquidacion = lq.id_liquidacion
                                JOIN tb_padron afi ON lq.id_afiliado = afi.id
                                JOIN tb_facturacion_datos fa ON lq.id_factura = fa.id_factura
                                LEFT JOIN tb_liquidaciones_tipo_motivos_debito mb ON dt.id_tipo_motivo_debito = mb.id_tipo_motivo_debito
                            WHERE
                                dt.id_detalle = ?
                            ", [$request->id]);

        return response()->json(["datos" => $cabecera[0] ?? null, "detalle" => $detalle]);
    }

    public function getDatosEditarliquidacionPractica(LiquidacionesRepository $repo, Request $request)
    {
        $cabecera = $repo->findByLiquidacionId($request->id);
        $detalle = $repo->findByLiquidacionDetalleId($request->id);

        return response()->json(["datos" => $cabecera[0] ?? null, "detalle" => $detalle]);
    }
}
