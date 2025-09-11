<?php

namespace App\Http\Controllers\Fiscalizacion;

use App\Models\Fiscalizacion\Expediente;
use App\Models\Fiscalizacion\HistorialExpediente;
use App\Models\PeriodoModelo;
use App\Models\Fiscalizacion\DeudaAporteEmpresa;
use App\Models\Fiscalizacion\DetallePeriodoExpediente;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ExpedienteController extends Controller
{





    public function postSaveExpediente(Request $request)
    {
        $msg = '';
        $user = Auth::user();

        $model = json_decode(file_get_contents($request->file('model')->getPathname()), true);
        $deudas = json_decode(file_get_contents($request->file('deudas')->getPathname()), true);

        if (!$model || !isset($model['empresa']['idEmpresa'])) {
            return response()->json(['message' => 'Datos inválidos o incompletos'], 400);
        }

        DB::beginTransaction();
        try {
            $expediente = null;

            // Buscar expediente por ID si viene
            if (!empty($model['idExpediente'])) {
                $expediente = Expediente::find($model['idExpediente']);
            }

            // Si no se encuentra por ID, intentamos por número
            if (!$expediente && !empty($model['numeroExpediente'])) {
                $expediente = Expediente::where('numero_expediente', $model['numeroExpediente'])->first();
            }

            // Si tampoco existe, lo creamos
            if (!$expediente) {
                $expediente = Expediente::create([
                    'id_empresa' => $model['empresa']['idEmpresa'],
                    'id_usuario' => $user->cod_usuario,
                    'numero_expediente' => $model['numeroExpediente'],
                    'fecha_creacion' => $model['fechaCreacion'],
                    'tipo_cuenta' => $model['instituciones']['idInstitucion'] ?? null,
                    'estado' => $model['vigente'],
                    'usuario_modifica' => $user->cod_usuario,
                ]);
                $msg = 'Expediente registrado correctamente';
            } else {
                // Actualización
                $expediente->update([
                    'id_empresa' => $model['empresa']['idEmpresa'],
                    'numero_expediente' => $model['numeroExpediente'],
                    'fecha_creacion' => $model['fechaCreacion'],
                    'tipo_cuenta' => $model['instituciones']['idInstitucion'] ?? null,
                    'estado' => $model['vigente'],
                    'usuario_modifica' => $user->cod_usuario,
                ]);
                $msg = 'Expediente actualizado correctamente';
            }

            // Crear array de id_deuda para eliminar los que ya no están
            $idsDeudas = array_column($deudas, 'id_deuda');

            // SOLO eliminar de detalles las deudas que ya no están en la nueva lista
            // NO eliminar del historial para mantener el registro histórico
            DetallePeriodoExpediente::where('id_expediente', $expediente->id_expediente)
                ->whereNotIn('id_deuda', $idsDeudas)
                ->delete();

            // Procesar cada deuda
            foreach ($deudas as $deuda) {
                $anio = $deuda['anio'];
                $mes = sprintf('%02d', $deuda['mes']);
                $periodo = $anio . '-' . $mes;
                $periodoDb = sprintf('%02d/%04d', $mes, $anio);

                // Buscar o crear el periodo en el formato correcto
                $periodoModel = PeriodoModelo::firstOrCreate(
                    ['periodo' => $periodoDb],
                    ['activo' => 1]
                );
                $idPeriodo = $periodoModel->id_periodo;

                // Registrar historial usando id_deuda como clave única
                // Solo crear si no existe, nunca actualizar para preservar el historial original
                HistorialExpediente::firstOrCreate(
                    [
                        'id_expediente' => $expediente->id_expediente,
                        'id_deuda' => $deuda['id_deuda']
                    ],
                    [
                        'periodo' => $periodo,
                        'monto_deuda' => $deuda['monto_deuda'] ?? 0,
                        'fecha_creacion' => now(),
                    ]
                );

                // Crear o actualizar el detalle del período usando id_deuda como clave única
                DetallePeriodoExpediente::updateOrCreate(
                    [
                        'id_expediente' => $expediente->id_expediente,
                        'id_deuda' => $deuda['id_deuda']
                    ],
                    [
                        'periodo' => $periodo,
                        'importe_sueldo' => $deuda['importe_sueldo'] ?? 0,
                        'aporte' => $deuda['aporte'] ?? 0,
                        'contribucion' => $deuda['contribucion'] ?? 0,
                        'contribucion_extraordinaria' => $deuda['contribucion_extraordinaria'] ?? 0,
                        'intereses' => $deuda['intereses'] ?? 0,
                        'monto_deuda' => $deuda['monto_deuda'] ?? 0,
                        'id_periodo' => $idPeriodo,
                        'fecha_modifica' => now(),
                        'usuario_modifica' => $user->cod_usuario,
                    ]
                );
            }

            DB::commit();
            return response()->json(['message' => $msg], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error al procesar el expediente',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getListExpedientes(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $usuario = $request->usuario;

        $query = Expediente::with(['empresa', 'usuario']);

        // Filtro: Empresa por ID (campo directo en la tabla)
        if ($request->filled('empresa')) {
            $query->where('id_empresa', $request->input('empresa'));
        }


        // Filtro: Fecha desde
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_creacion', '>=', $request->input('fecha_desde'));
        }

        // Filtro: Fecha hasta
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_creacion', '<=', $request->input('fecha_hasta'));
        }

        // Filtro: CUIT (relación con empresa)
        if ($request->filled('cuit')) {
            $query->whereHas('empresa', function ($q) use ($request) {
                $q->where('cuit', 'like', '%' . $request->input('cuit') . '%');
            });
        }

        // Filtro: Razón Social (relación con empresa)
        if ($request->filled('razon_social')) {
            $query->whereHas('empresa', function ($q) use ($request) {
                $q->where('razon_social', 'like', '%' . $request->input('razon_social') . '%');
            });
        }

        // Filtro: Usuario (relación con usuario)
        if ($request->filled('usuario')) {
            $query->whereHas('usuario', function ($q) use ($request) {
                $q->where('nombre_apellidos', 'like', '%' . $request->input('usuario') . '%');
                //   ->orWhere('email', 'like', '%' . $request->input('usuario') . '%');
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        // Filtro: Deuda OSPF
        if ($request->filled('deuda_ospf')) {
            $query->where('deuda_ospf', 'like', '%' . $request->input('deuda_ospf') . '%');
        }


        // Ejecutamos la paginación
        $result = $query->orderBy('fecha_creacion', 'desc')->paginate($perPage, ['*'], 'page', $page);

        return response()->json($result, 200);
    }



    public function getExpedienteById($id)
    {
        $expediente = Expediente::with(['empresa', 'usuario', 'periodos', 'historial'])->find($id);

        if (!$expediente) {
            return response()->json(['message' => 'Expediente no encontrado'], 404);
        }

        $response = [
            'idExpediente' => $expediente->id_expediente,
            'numeroExpediente' => $expediente->numero_expediente,
            'fechaCreacion' => $expediente->fecha_creacion,
            'vigente' => $expediente->estado === 'Vigente' ? 1 : 0,
            'instituciones' => [
                'idInstitucion' => $expediente->tipo_cuenta
            ],
            'usuarioRegistra' => $expediente->usuario->nombre_apellidos ?? '',
            'empresa' => [
                'idEmpresa' => $expediente->empresa->id_empresa ?? null,
                'cuit' => $expediente->empresa->cuit ?? '',
                'razonSocial' => $expediente->empresa->razon_social ?? '',
                'nombreFantasia' => $expediente->empresa->nombre_fantasia ?? '',
                'sindicato' => $expediente->empresa->sindicato ?? ''
            ],
            'detalle' => $expediente->periodos->map(function ($item) {
                // Extraer año y mes del período
                $anio = null;
                $mes = null;
                if (preg_match('/^(\d{4})-(\d{2})$/', $item->periodo, $matches)) {
                    $anio = $matches[1];
                    $mes = $matches[2];
                }

                return [
                    'id_deuda' => $item->id_deuda,
                    'anio' => $anio,
                    'mes' => $mes,
                    'periodo' => $item->periodo,
                    'importe_sueldo' => $item->importe_sueldo,
                    'aporte' => $item->aporte,
                    'contribucion' => $item->contribucion,
                    'contribucion_extraordinaria' => $item->contribucion_extraordinaria,
                    'intereses' => $item->intereses,
                    'monto_deuda' => $item->monto_deuda,
                    'tipo_deuda' => 'APORTE', // Valor por defecto
                    'estado' => 'Vigente' // Valor por defecto
                ];
            }),
            'Periodos' => $expediente->periodos->map(function ($item) {
                return [
                    'id_deuda' => $item->id_deuda,
                    'label' => $item->periodo,
                    'value' => $item->periodo,
                    'montoDeuda' => $item->monto_deuda,
                    'fechaModifica' => $item->fecha_modifica,
                    'importe_sueldo' => $item->importe_sueldo,
                    'aporte' => $item->aporte,
                    'contribucion' => $item->contribucion,
                    'contribucionExtraordinaria' => $item->contribucion_extraordinaria,
                    'intereses' => $item->intereses,
                    'usuarioModifica' => $item->usuario_modifica,
                    'idExpediente' => $item->id_expediente
                ];
            }),
            'historialExpedientes' => $expediente->historial->map(function ($item) {
                return [
                    'id_historial' => $item->id_historial,
                    'id_expediente' => $item->id_expediente,
                    'periodo' => $item->periodo,
                    'montoDeuda' => $item->monto_deuda,
                    'fechaModifica' => $item->fecha_creacion
                ];
            }),
        ];

        return response()->json($response, 200);
    }





    public function generarNumeroExpediente()
    {
        // Obtener el año y el mes actual
        $anio = date('Y');
        $mes = date('m');

        // Buscar el último número de expediente generado para el año y mes actual
        $ultimoExpediente = Expediente::whereYear('fecha_creacion', $anio)
            ->whereMonth('fecha_creacion', $mes)
            ->orderBy('numero_expediente', 'desc')
            ->first();

        // Si existe un último expediente, extraemos el número secuencial
        if ($ultimoExpediente) {
            $ultimoNumero = $ultimoExpediente->numero_expediente;
            $numeroSecuencial = (int) substr($ultimoNumero, -3) + 1;  // Extraemos el último número y sumamos 1
        } else {
            // Si no existe ningún expediente, el número secuencial comienza en 1
            $numeroSecuencial = 1;
        }

        // Formateamos el número de expediente (Año-Mes-NumeroSecuencial)
        $numeroExpediente = sprintf("%s-%s-%03d", $anio, $mes, $numeroSecuencial);

        return response()->json(['numero' => $numeroExpediente], 200);
    }

    public function getExpedientesByIdEmpresa($idEmpresa)
    {
        $expedientes = Expediente::with(['empresa', 'usuario', 'periodos'])
            ->where('id_empresa', $idEmpresa)
            ->where('estado', 'Vigente')
            ->get();

        if ($expedientes->isEmpty()) {
            return response()->json(['message' => 'No se encontraron expedientes para esta empresa'], 404);
        }

        $response = $expedientes->map(function ($expediente) {
            return [
                'idExpediente' => $expediente->id_expediente,
                'numeroExpediente' => $expediente->numero_expediente,
                'fechaCreacion' => $expediente->fecha_creacion,
                'vigente' => $expediente->estado === 'Vigente' ? 1 : 0,
                'usuarioRegistra' => $expediente->usuario->nombre_apellidos ?? '',
                'empresa' => [
                    'idEmpresa' => $expediente->empresa->id_empresa ?? null,
                    'cuit' => $expediente->empresa->cuit ?? '',
                    'razonSocial' => $expediente->empresa->razon_social ?? '',
                    'nombreFantasia' => $expediente->empresa->nombre_fantasia ?? '',
                    'sindicato' => $expediente->empresa->sindicato ?? ''
                ],
                'label' => $expediente->numero_expediente, // para usar en select
                'value' => $expediente->id_expediente        // para usar en select
            ];
        });

        return response()->json($response, 200);
    }

    public function buscarDeudasPorExpediente($idExpediente)
    {
        try {
            // Cargar el expediente junto con todos los detalles de los periodos asociados
            $expediente = Expediente::with('periodos')->find($idExpediente);

            // Verificar si el expediente existe
            if (!$expediente) {
                return response()->json([
                    'message' => 'Expediente no encontrado'
                ], 404);
            }

            // Obtener todos los periodos y transformarlos al formato que espera el frontend
            $periodos = $expediente->periodos->map(function ($periodo) {
                // Extraer año y mes del string periodo
                $anio = null;
                $mes = null;
                $valorPeriodo = trim($periodo->periodo);

                // Solo soporta 'YYYY-MM'
                if (preg_match('/^(\d{4})-(\d{2})$/', $periodo->periodo, $matches)) {
                    $anio = $matches[1];
                    $mes = $matches[2];
                }

                return [
                    'id_deuda' => $periodo->id_deuda,
                    'anio' => $anio,
                    'mes' => $mes,
                    'periodo' => $valorPeriodo,
                    'importe_sueldo' => $periodo->importe_sueldo,
                    'aporte' => $periodo->aporte,
                    'contribucion' => $periodo->contribucion,
                    'contribucion_extraordinaria' => $periodo->contribucion_extraordinaria,
                    'intereses' => $periodo->intereses,
                    'monto_deuda' => $periodo->monto_deuda,
                    'tipo_deuda' => 'APORTE', // Valor por defecto
                    'estado' => 'Vigente', // Valor por defecto
                    'fecha_recalculo' => $periodo->fecha_modifica,
                    'fecha_vencimiento' => null, // No disponible en esta tabla
                    'id_empresa' => null, // Se puede obtener del expediente si es necesario
                    'monto_estudio_juridico' => null,
                    'monto_gestion_morosidad' => null,
                ];
            });

            // Devolver la respuesta en el formato adecuado
            return response()->json($periodos);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDeudaTotalByExpedienteId($idExpediente)
    {
        try {
            // Buscar el expediente con sus periodos asociados
            $expediente = Expediente::with('periodos')->find($idExpediente);

            if (!$expediente) {
                return response()->json(['message' => 'Expediente no encontrado'], 404);
            }

            // Sumar el monto de deuda de todos los periodos
            $deudaTotal = $expediente->periodos->sum('monto_deuda');

            return response()->json([
                'idExpediente' => $idExpediente,
                'deudaTotal' => $deudaTotal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al calcular la deuda total',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}