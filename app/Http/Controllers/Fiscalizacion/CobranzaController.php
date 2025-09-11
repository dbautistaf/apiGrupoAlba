<?php

namespace App\Http\Controllers\Fiscalizacion;

use App\Models\Fiscalizacion\Cobranza;
use App\Models\Fiscalizacion\Intimacion;
use App\Models\Fiscalizacion\DeudaAporteEmpresa;
use App\Models\Fiscalizacion\DetallePeriodoExpediente;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Fiscalizacion\Expediente;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Utils\ManejadorDeArchivosUtils;
use App\Models\Fiscalizacion\ArchivoCobranza;





class CobranzaController extends Controller
{

    public function listarCobranzas(Request $request)
    {
        // Parseo de fechas
        $desde = Carbon::parse($request->desde)->startOfDay();
        $hasta = Carbon::parse($request->hasta)->endOfDay();

        // Parámetros de filtro
        $empresa = $request->empresa;
        $usuario = $request->usuario;

        // Parámetros de paginación
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        // Construcción de la consulta
        $query = Cobranza::with(['empresa', 'expediente'])
            ->when(
                $empresa,
                fn($q) =>
                $q->where('id_empresa', $empresa)
            )
            ->when(
                $request->filled('desde'),
                fn($q) =>
                $q->whereDate('fecha_creacion', '>=', $desde)
            )
            ->when(
                $request->filled('hasta'),
                fn($q) =>
                $q->whereDate('fecha_creacion', '<=', $hasta)
            )
            ->where('estado', 1);

        if ($usuario) {
            $query->where('usuario', 'like', '%' . $usuario . '%');
        }

        // Ejecución de la consulta con paginación
        $result = $query->orderBy('fecha_creacion', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Transformación de los datos
        $data = $result->getCollection()->transform(function ($item) {
            return [
                'idCobranza' => $item->id_cobranza,
                'cobroNeto' => $item->cobro_neto,
                'cobroTotal' => $item->cobro_total,
                'fechaCreacion' => $item->fecha_creacion,
                'usuario' => $item->usuario,
                'empresa' => [
                    'cuit' => $item->empresa->cuit ?? '',
                    'razonSocial' => $item->empresa->razon_social ?? '',
                ],
                'expediente' => [
                    'numeroExpediente' => $item->expediente->numero_expediente ?? '',
                ],
                'archivos' => $item->archivos->map(function ($archivo) {
                    return [
                        'idArchivo' => $archivo->id ?? null,
                        'idCobranza' => $archivo->id_cobranza ?? null,
                        'nombre' => $archivo->nombre_original ?? '',
                        'ruta' => $archivo->ruta ?? '',
                        'tipo' => $archivo->tipo_archivo ?? '',
                        'tamanio' => $archivo->tamaño ?? null,
                        'fechaSubida' => $archivo->fecha_subida ?? null,
                    ];
                }),
            ];
        });

        // Respuesta JSON
        return response()->json([
            'data' => $data,
            'total' => $result->total(),
            'current_page' => $result->currentPage(),
            'per_page' => $result->perPage(),
        ]);
    }


    public function postSaveCobranza(Request $request, ManejadorDeArchivosUtils $storageFile)
    {
        $user = auth()->user();
        $data = $request->all();

        // Decodificar los campos que vienen como string JSON
        $empresa = is_string($data['empresa'] ?? null) ? json_decode($data['empresa'], true) : ($data['empresa'] ?? []);
        $expediente = is_string($data['expediente'] ?? null) ? json_decode($data['expediente'], true) : ($data['expediente'] ?? []);
        $bancoCobranza = is_string($data['bancoCobranza'] ?? null) ? json_decode($data['bancoCobranza'], true) : ($data['bancoCobranza'] ?? []);
        $medioDePago = is_string($data['medioDePago'] ?? null) ? json_decode($data['medioDePago'], true) : ($data['medioDePago'] ?? []);
        $instituciones = is_string($data['instituciones'] ?? null) ? json_decode($data['instituciones'], true) : ($data['instituciones'] ?? []);


        // Mapeo del payload según datos del frontend
        $payload = [
            'id_cobranza' => $data['idCobranza'] ?? null,
            'id_expediente' => $data['idExpediente'] ?? ($expediente['idExpediente'] ?? null),
            'id_empresa' => $data['idEmpresa'] ?? ($empresa['idEmpresa'] ?? null),
            'id_institucion' => $data['idInstitucion'] ?? ($instituciones['idInstitucion'] ?? null),
            'id_forma_pago' => $data['formaPago'] ?? ($medioDePago['formaPago'] ?? null),
            'id_banco_cobranza' => $data['idBancoCobranza'] ?? ($bancoCobranza['idBancoCobranza'] ?? null),
            'numero_cheque' => $data['numeroCheque'] ?? null,
            'numero_transferencia' => $data['numeroTransferencia'] ?? null,
            'numero_recibo' => $data['numeroRecibo'] ?? null,
            'fecha_pago' => $data['fechaPago'] ?? null,
            'fecha_creacion' => $data['fechaCreacion'] ?? now(),
            'honorarios' => isset($data['honorarios']) ? (float) $data['honorarios'] : 0,
            'gasto_mora' => isset($data['gastoMora']) ? (float) $data['gastoMora'] : 0,
            'intereses_moratorios' => isset($data['interesesMoratorios']) ? (float) $data['interesesMoratorios'] : 0,
            'intereses_financiacion' => isset($data['interesesFinanciacion']) ? (float) $data['interesesFinanciacion'] : 0,
            'importe_sueldo' => isset($data['importe_sueldo']) ? (float) $data['importe_sueldo'] : 0,
            'aporte' => isset($data['aporte']) ? (float) $data['aporte'] : 0,
            'contribucion' => isset($data['contribucion']) ? (float) $data['contribucion'] : 0,
            'aporte_snr' => isset($data['aporteSNR']) ? (float) $data['aporteSNR'] : 0,
            'contribucion_extraordinaria' => isset($data['contribucionExtraordinaria']) ? (float) $data['contribucionExtraordinaria'] : 0,
            'bonificacion' => isset($data['bonificacion']) ? (float) $data['bonificacion'] : 0,
            'cobro_total' => isset($data['cobroTotal']) ? (float) $data['cobroTotal'] : 0,
            'cobro_neto' => isset($data['cobroNeto']) ? (float) $data['cobroNeto'] : 0,
            'plan_pago' => $data['planPago'] ?? 'NO',
            'comision' => $data['comision'] ?? 'NO',
            'observacion' => $data['observacion'] ?? '',
            'usuario' => $user->nombre_apellidos ?? 'Sistema',
        ];


        DB::beginTransaction();

        try {
            if (empty($payload['id_cobranza'])) {
                unset($payload['id_cobranza']); // Evita conflictos con PK
                $cobranza = Cobranza::create($payload);

                // Marcar expediente como no vigente al crear la cobranza del mismo
                $expediente = Expediente::findOrFail($payload['id_expediente']);
                $expediente->estado = 'Finalizado';
                $expediente->save();

                // Marcar todas las intimaciones del expediente como finalizadas
                Intimacion::where('id_expediente', $payload['id_expediente'])
                    ->update(['estado' => 'Finalizado']);

                // Obtener los id_deuda de los periodos del expediente
                $idsDeuda = DetallePeriodoExpediente::where('id_expediente', $payload['id_expediente'])
                    ->pluck('id_deuda'); // Ej: [123, 124, 125]

                // Finalizar solo esas deudas
                DeudaAporteEmpresa::whereIn('id_deuda', $idsDeuda)
                    ->update(['estado' => 'Finalizado']);


                $msg = 'La gestión se ha registrado correctamente.';
            } else {
                $cobranza = Cobranza::findOrFail($payload['id_cobranza']);
                unset($payload['id_cobranza']);
                $cobranza->update($payload);

                $msg = 'La gestión se ha actualizado correctamente.';
            }

            //Guardado de archivos adjuntos
            if (count($request->archivos) > 0) {
                $archivosAdjuntos = $storageFile->findByCargaMasivaArchivos(
                    "COBRANZA_" . $cobranza->id_cobranza,
                    'fiscalizacion/cobranza/archivos',
                    $request
                );

                $this->guardarArchivosAdjuntosCobranza($archivosAdjuntos, $cobranza->id_cobranza);
            }

            DB::commit();

            return response()->json([
                'message' => $msg,
                'model' => $cobranza,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Ocurrió un error al guardar la cobranza.',
                'error' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    // 'trace' => $e->getTrace(), // Solo si estás en modo debug
                ]
            ], 500);
        }
    }




    public function getExpedientesById($id)
    {
        $cobranza = Cobranza::with('expediente')->find($id);

        if (!$cobranza) {
            return response()->json([
                'message' => 'Cobranza no encontrada'
            ], 404);
        }

        return response()->json([
            'expediente' => $cobranza->expediente
        ]);
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

            // Obtener todos los periodos y transformarlos a camelCase
            $periodos = $expediente->periodos->map(function ($periodo) {
                return [
                    'mes' => $periodo->mes, // Asumiendo que el campo mes está en el modelo
                    'anio' => $periodo->anio, // Asumiendo que el campo año está en el modelo
                    'montoDeuda' => $periodo->monto_deuda,
                    'importe_sueldo' => $periodo->importe_sueldo,
                    'aporte' => $periodo->aporte,
                    'contribucion' => $periodo->contribucion,
                    'contribucionExtraordinaria' => $periodo->contribucion_extraordinaria,
                    'intereses' => $periodo->intereses,
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

    public function getCobranzaById($id)
    {
        $item = Cobranza::with([
            'empresa',
            'expediente',
            'bancosCobranza',
            'formasPago',
            'archivos',
        ])->find($id);

        if (!$item) {
            return response()->json(['error' => 'Cobranza no encontrada'], 404);
        }

        $resultado = [
            'idCobranza' => $item->id_cobranza,
            'fechaPago' => $item->fecha_pago,
            'fechaCreacion' => $item->fecha_creacion,
            'numeroCheque' => $item->numero_cheque,
            'numeroTransferencia' => $item->numero_transferencia,
            'numeroRecibo' => $item->numero_recibo,
            'honorarios' => $item->honorarios,
            'gastoMora' => $item->gasto_mora,
            'interesesFinanciacion' => $item->intereses_financiacion,
            'interesesMoratotios' => $item->intereses_moratorios,
            'importe_sueldo' => $item->importe_sueldo,
            'aporte' => $item->aporte,
            'contribucion' => $item->contribucion,
            'aporteSNR' => $item->aporte_snr,
            'contribucionExtraordinaria' => $item->contribucion_extraordinaria,
            'bonificacion' => $item->bonificacion,
            'cobroTotal' => $item->cobro_total,
            'cobroNeto' => $item->cobro_neto,
            'planPago' => $item->plan_pago,
            'comision' => $item->comision,
            'observacion' => $item->observacion,
            'usuario' => $item->usuario,

            'empresa' => [
                'idEmpresa' => $item->empresa->id_empresa ?? null,
                'cuit' => $item->empresa->cuit ?? null,
                'razonSocial' => $item->empresa->razon_social ?? null,
                'nombreFantasia' => $item->empresa->nombre_fantasia ?? null,
            ],

            'expediente' => [
                'idExpediente' => $item->expediente->id_expediente ?? null,
                'numeroExpediente' => $item->expediente->numero_expediente ?? null,
            ],

            'bancoCobranza' => [
                'idBancoCobranza' => $item->bancosCobranza->id_banco_cobranza ?? null,
                'descripcion' => $item->bancosCobranza->descripcion ?? null,
            ],

            'medioDePago' => [
                'formaPago' => $item->formasPago->id_forma_pago ?? null,
                'descripcion' => $item->formasPago->descripcion ?? null,
            ],
            'archivos' => $item->archivos->map(function ($archivo) {
                return [
                    'idArchivo' => $archivo->id ?? null,
                    'idCobranza' => $archivo->id_cobranza ?? null,
                    'nombre' => $archivo->nombre_original ?? '',
                    'ruta' => $archivo->ruta ?? '',
                    'tipo' => $archivo->tipo_archivo ?? '',
                    'tamanio' => $archivo->tamaño ?? null,
                    'fechaSubida' => $archivo->fecha_subida ?? null,
                ];
            }),

        ];

        return response()->json($resultado, 200);
    }

    public function eliminarCobranza($id)
    {
        try {
            $cobranza = Cobranza::findOrFail($id);

            // Marcamos la cobranza como inactiva
            $cobranza->estado = 0;
            $cobranza->save();

            // reactivar el expediente vinculado
            if ($cobranza->id_expediente) {
                $expediente = Expediente::find($cobranza->id_expediente);
                if ($expediente) {
                    $expediente->estado = 'Vigente'; // o el estado que uses
                    $expediente->save();
                }
            }

            return response()->json([
                'message' => 'Cobranza eliminada correctamente.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la cobranza.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    //Metodos de Comprobantes


    private function guardarArchivosAdjuntosCobranza(array $archivosAdjuntos, int $idCobranza)
    {
        foreach ($archivosAdjuntos as $archivo) {
            \Log::info('Archivo recibido:', $archivo);
            ArchivoCobranza::create([
                'id_cobranza' => $idCobranza,
                'nombre_original' => $archivo['nombre'] ?? 'archivo_sin_nombre',
                'ruta' => str_replace('public/', '', $archivo['ruta']),
                'tipo_archivo' => $archivo['extension'] ?? pathinfo($archivo['nombre'], PATHINFO_EXTENSION),

                'tamaño' => $archivo['tamaño'] ?? null,
                'fecha_subida' => now(),
            ]);
        }
    }



    public function getArchivosPorCobranza($id)
    {
        $archivos = ArchivoCobranza::where('id_cobranza', $id)->get();
        return response()->json($archivos, 200);
    }




    public function getArchivoAdjunto(ManejadorDeArchivosUtils $storageFile, Request $request)
    {
        $path = "fiscalizacion/cobranza/archivos/";
        // $data = $pago->findById($request->id);
        $anioTrabaja = $request->fecha_registra;
        $path .= "{$anioTrabaja}/$request->nombre_archivo";

        return $storageFile->findByObtenerArchivo($path);
    }
}