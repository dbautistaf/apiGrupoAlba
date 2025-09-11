<?php

namespace App\Http\Controllers\Contabilidad\Services;

use App\Http\Controllers\Contabilidad\Repository\AsientoContableRepository;
use App\Http\Controllers\Contabilidad\Repository\PeriodosContablesRepository;
use App\Http\Controllers\Utils\CorrelativosOspfRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class AsientoContableController extends Controller
{

    private $asientoContableRepository;
    private $correlativosOspfRepository;
    private $periodoContableRepositorio;
    private $periodoContableActivo;

    public function __construct(
        PeriodosContablesRepository $periodoContableRepositorio,
        AsientoContableRepository $asientoContableRepository,
        CorrelativosOspfRepository $correlativosOspfRepository
    ) {
        $this->asientoContableRepository = $asientoContableRepository;
        $this->correlativosOspfRepository = $correlativosOspfRepository;
        $this->periodoContableActivo = $periodoContableRepositorio->findByPeriodoContableActivo();
    }


    public function getListar(Request $request)
    {
        $data = $this->asientoContableRepository->findByListar($request);
        return response()->json($data);
    }

    public function getBuscarId(Request $request)
    {
        return response()->json($this->asientoContableRepository->findById($request->id));
    }

    public function getEliminarDetalleId(Request $request)
    {
        $this->asientoContableRepository->findByDeleteDetalleId($request->id);
        return response()->json(['message' => 'Registro eliminado correctamente']);
    }

    public function getAnularAsientoContableId(Request $request)
    {
        $this->asientoContableRepository->findByAnularAsientoContableId($request->id, $request->estado);
        return response()->json(['message' => 'Registro anulado correctamente']);
    }

    public function getProcesar(Request $request)
    {
        DB::beginTransaction();
        try {

            if (is_null($request->id_asiento_contable)) {

                $numeroCorrelativo = $this->correlativosOspfRepository->findByObtenerCorrelativo(1);
                $idPeriodoActivo =   $this->periodoContableActivo->id_periodo_contable;

                $asiento = $this->asientoContableRepository->findByCrearAsiento(
                    $request->id_tipo_asiento,
                    $request->asiento_modelo,
                    $request->asiento_leyenda,
                    $numeroCorrelativo,
                    $idPeriodoActivo,
                    $request->numero_referencia,
                    $request->vigente
                );

                $this->correlativosOspfRepository->findByGuardarCorrelativo(1, $numeroCorrelativo);

                foreach ($request->detalle as $key) {
                    $this->asientoContableRepository->findByCrearDetalleAsiento(
                        [
                            "id_asiento_contable" => $asiento->id_asiento_contable,
                            "id_proveedor_cuenta_contable" => $key['id_proveedor_cuenta_contable'],
                            "id_forma_pago_cuenta_contable" => $key['id_forma_pago_cuenta_contable'],
                            "monto_debe" =>  $key['monto_debe'],
                            "monto_haber" => $key['monto_haber'],
                            "observaciones" => $key['observaciones'],
                            "id_detalle_plan" => $key['id_detalle_plan']
                        ]
                    );
                }

                //@CONTRAASIENTO
                if (!is_null($request->numero_referencia)) {
                    $this->asientoContableRepository->findByContraAsientoContableId($request->numero_referencia, $numeroCorrelativo, 'CONTRAASIENTO');
                }

                DB::commit();
                return response()->json(['message' => 'Registro procesado correctamente'], 200);
            } else {
                $this->asientoContableRepository->findByUpdateAsiento(
                    $request->id_tipo_asiento,
                    $request->fecha_asiento,
                    $request->asiento_modelo,
                    $request->asiento_leyenda,
                    $request->numero,
                    $request->id_periodo_contable,
                    $request->numero_referencia,
                    $request->asiento_observaciones,
                    $request->id_asiento_contable
                );

                foreach ($request->detalle as $key) {
                    $this->asientoContableRepository->findByUpdateDetalleItemAsiento(
                        [
                            "id_asiento_contable" => $key['id_asiento_contable'],
                            "id_proveedor_cuenta_contable" => $key['id_proveedor_cuenta_contable'],
                            "id_forma_pago_cuenta_contable" => $key['id_forma_pago_cuenta_contable'],
                            "monto_debe" =>  $key['monto_debe'],
                            "monto_haber" => $key['monto_haber'],
                            "observaciones" => $key['observaciones'],
                            "id_detalle_plan" => $key['id_detalle_plan']
                        ],
                        $key['id_asiento_contable_detalle']
                    );
                }
                DB::commit();
                return response()->json(['message' => 'Registro modificado correctamente'], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
