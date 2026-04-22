<?php

namespace App\Http\Controllers\Tesoreria\Services;

use App\Http\Controllers\Tesoreria\Dto\EmisionesChequeDto;
use App\Http\Controllers\Tesoreria\Repository\TesChequerasBancariasRepository;
use App\Models\Tesoreria\TesPagoEntity;
use App\Models\Tesoreria\TestChequesEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ChequerasBancariasController extends Controller
{

    private TesChequerasBancariasRepository $repoChequera;
    public function __construct(TesChequerasBancariasRepository $repoChequera)
    {
        $this->repoChequera = $repoChequera;
    }

    public function listar(Request $request)
    {
        $data = [];

        $data = $this->repoChequera->findByListChequeras($request);
        return response()->json($data);
    }

    public function tipoChequera(Request $request)
    {
        return response()->json($this->repoChequera->findByLisTipoChequeras());
    }

    public function proceso(Request $request)
    {
        try {
            DB::beginTransaction();

            if (!is_null($request->id_chequera)) {
                $this->repoChequera->findByModificar($request);
                DB::commit();
                return response()->json(['message' => 'Chequera modificada con éxito']);
            } else {
                $this->repoChequera->findByCreate($request);
                DB::commit();
                return response()->json(['message' => 'Chequera registrada con éxito']);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al procesar una chequera', 'error' => $th->getMessage()], 500);
        }
    }

    public function eliminar(Request $request)
    {
        $this->repoChequera->findByEliminar($request->id);

        return response()->json(['message' => 'Chequera eliminada con éxito']);
    }

    public function estado(Request $request)
    {
        $this->repoChequera->findByUpdateEstado($request->id, $request->estado);

        return response()->json(['message' => 'Estado modificado con éxito']);
    }

    public function historialChequera(Request $request)
    {
        $historial = [];
        $cheques = [];
        $pagos = [];
        $cheques = TestChequesEntity::with(['usuario'])
            ->where('id_chequera', $request->id)->get();

        if (count($cheques) > 0) {
            foreach ($cheques as $value) {
                $historial[] = new EmisionesChequeDto(
                    $value->numero_cheque,
                    $value->fecha_emision,
                    $value->beneficiario,
                    $value->monto,
                    $value->estado,
                    'MOD. CHEQUE',
                    $value->usuario->nombre_apellidos
                );
            }
        }

        $pagos = TesPagoEntity::with(['usuario', 'opa', 'estado', 'opa.prestador', 'opa.proveedor', 'usuario'])
            ->where('id_chequera', $request->id)
            ->get();
        if (count($pagos) > 0) {
            foreach ($pagos as $value) {
                $historial[] = new EmisionesChequeDto(
                    $value->num_cheque,
                    $value->fecha_confirma_pago,
                    !is_null($value->opa->prestador) ? $value->opa->prestador->razon_social : $value->opa->proveedor->razon_social,
                    $value->monto_pago,
                    $value->estado->descripcion_estado,
                    $value->num_pago,
                    $value->usuario->nombre_apellidos
                );
            }
        }

        return response()->json($historial);
    }
}
