<?php

namespace App\Http\Controllers\Fiscalizacion;

use App\Models\Fiscalizacion\AcuerdoPago;
use App\Models\Fiscalizacion\Cuota;
use App\Models\Fiscalizacion\AcuerdoPagoPeriodo;
use App\Models\Fiscalizacion\Expediente;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AcuerdoPagoController extends Controller
{
    public function postSaveAcuerdoPago(Request $request)
    {
        $user = Auth::user();
        DB::beginTransaction(); // Inicio de transacción

        try {
            $acuerdoPago = null;

            if (empty($request->id_acuerdo_pago)) {
                // 1. Crear acuerdo de pago
                $acuerdoPago = AcuerdoPago::create([
                    'id_empresa'         => $request->empresa['idEmpresa'],
                    'id_estado_acuerdo'  => 1, // o el valor que corresponda por defecto
                    'numero_acta'        => $request->numeroActa,
                    'fecha_creacion'     => $request->fechaCreacion,
                    'importe_total'      => $request->montoTotal,
                    'usuario' => $user->nombre_apellidos,
                    'id_expediente' => $request->expediente['idExpediente'],
                ]);
                // Cambiar estado del expediente a "Acuerdo"
                if ($acuerdoPago->id_expediente) {
                    $expediente = Expediente::find($acuerdoPago->id_expediente);
                    if ($expediente) {
                        $expediente->estado = 'Acuerdo';
                        $expediente->save();
                    }
                }
            } else {
                // 1b. Actualizar si ya existe
                $acuerdoPago = AcuerdoPago::find($request->id_acuerdo_pago);
                $acuerdoPago->update([
                    'id_empresa'         => $request->empresa['idEmpresa'],
                    'id_estado_acuerdo'  => $request->id_estado_acuerdo,
                    'numero_acta'        => $request->numeroActa,
                    'fecha_creacion'     => $request->fechaCreacion,
                    'importe_total'      => $request->montoTotal,
                    'usuario' => $user->nombre_apellidos,
                    'id_expediente' => $request->expediente['idExpediente'],
                ]);

                // Opcional: eliminar cuotas/periodos existentes si estás haciendo update
                $acuerdoPago->cuotas()->delete();
                $acuerdoPago->periodos()->delete();
            }

            // 2. Guardar cuotas
            foreach ($request->cuotasDetalle as $cuota) {
                Cuota::create([
                    'id_acuerdo_pago'   => $acuerdoPago->id_acuerdo_pago,
                    'numero_cuota'      => $cuota['mes'],
                    'importe_cuota'     => $cuota['cuotaFija'],
                    'capital_cuota'     => $cuota['capital'],
                    'interes_cuota'   => $cuota['interes'],
                    'fecha_vencimiento' => $cuota['vencimiento'],
                    'estado'            => 'Pendiente' // o lo que corresponda
                ]);
            }

            // 3. Guardar relación con periodos
            foreach ($request->periodosDetalle as $periodo) {
                AcuerdoPagoPeriodo::create([
                    'id_acuerdo_pago' => $acuerdoPago->id_acuerdo_pago,
                    'id_periodo'      => $periodo['idDeuda'], // o id_periodo si es otro campo
                    'monto_asociado'  => $periodo['monto_deuda'],
                ]);
            }

            DB::commit(); // Confirmar transacción
            return response()->json(['message' => 'Acuerdo de pago registrado correctamente'], 200);

        } catch (\Exception $e) {
            DB::rollBack(); // Revertir todo en caso de error
            return response()->json(['message' => 'Error al guardar acuerdo de pago', 'error' => $e->getMessage()], 500);
        }
    }

    public function getListAcuerdosPago(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $usuario = $request->input('usuario');

        $query = AcuerdoPago::with(['empresa', 'estado'])
            ->where('id_estado_acuerdo', '!=', 5);
        // Opcional: aplicar filtros si querés
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_creacion', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_creacion', '<=', $request->fecha_hasta);
        }
        if ($request->filled('estado')) {
            $query->whereHas('estado', function ($q) use ($request) {
            $q->where('descripcion', $request->estado);
        });
        }
        if ($request->filled('empresa')) {
            $query->where('id_empresa', $request->empresa);
        }
        if ($usuario) {
            $query->where('usuario', 'like', '%' . $usuario . '%');
        }


        // Ejecutamos la paginación
            $result = $query->orderBy('fecha_creacion', 'desc')->paginate($perPage, ['*'], 'page', $page);

        // Transformar los datos
        $data = $result->getCollection()->transform(function ($item) {
            return [
                'idAcuerdoPago' => $item->id_acuerdo_pago,
                'numeroActa'    => $item->numero_acta,
                'empresa'       => $item->empresa->razon_social ?? '',
                'cuit'          => $item->empresa->cuit ?? '',
                'fecha'         => $item->fecha_creacion,
                'importeTotal'  => $item->importe_total,
                'estado'        => $item->estado->descripcion ?? '',
                'usuario'        => $item->usuario ?? '',
                'id_expediente' => $item->id_expediente,
            ];
        });

        return response()->json([
            'data' => $data,
            'total' => $result->total(),
            'current_page' => $result->currentPage(),
            'per_page' => $result->perPage(),
        ]);

    }





    public function getAcuerdoPagoById($id)
    {
        $query = AcuerdoPago::with('empresa')->with('estadoAcuerdo')->find($id);
        return response()->json($query, 200);
    }


    public function generarNumeroActa()
    {
        try {
            $anio = Carbon::now()->format('Y');

            // Buscar el último número de acta para el año actual
            $ultimo = DB::table('tb_fisca_acuerdo_pago')
                ->where('numero_acta', 'like', "$anio-%")
                ->orderByDesc('numero_acta')
                ->first();

            // Extraer el correlativo y sumarle 1
            $nuevoNumero = 1;

            if ($ultimo) {
                $partes = explode('-', $ultimo->numero_acta);
                $nuevoNumero = intval($partes[1]) + 1;
            }

            // Formatear con ceros a la izquierda
            $numeroFormateado = str_pad($nuevoNumero, 5, '0', STR_PAD_LEFT);
            $nuevoActa = "$anio-$numeroFormateado";

            return response()->json([
                'numero_acta' => $nuevoActa
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error generando número de acta',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function eliminarAcuerdo($id)
    {
        DB::beginTransaction();
        try {
            $AcuerdoPago = AcuerdoPago::findOrFail($id);

            // Marcamos el acuerdo como eliminado
            $AcuerdoPago->id_estado_acuerdo = 5;
            $AcuerdoPago->save();

            // pasar cuotas del acuerdo a estado eliminado
            $AcuerdoPago->cuotas()->update(['estado' => 'Eliminado']);

            // Volver a poner el estado del expediente en "Vigente"
            if ($AcuerdoPago->id_expediente) {
                $expediente = Expediente::find($AcuerdoPago->id_expediente);
                if ($expediente) {
                    $expediente->estado = 'Vigente';
                    $expediente->save();
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'Acuerdo pago y cuotas eliminados correctamente.',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al eliminar el acuerdo.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}