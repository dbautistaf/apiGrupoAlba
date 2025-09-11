<?php

namespace App\Http\Controllers\Fiscalizacion;

use App\Models\Fiscalizacion\Intimacion;
use App\Models\Fiscalizacion\TipoMovimiento;
use App\Models\Fiscalizacion\Institucion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class IntimacionController extends Controller
{
    
    public function postSaveIntimacion(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();
        
        $payload = [
            'id_intimacion' => $data['codIntimacion'] ?? null,
            'atendido_por' => $data['atendidoPor'] ?? null,
            'sector' => $data['sector'] ?? null,
            'telefono_farmacia' => $data['telefonoFarmacia'] ?? null,
            'fecha_inicio_gestion' => Carbon::now(), // Usamos la fecha actual
            'fecha_vencimiento_gestion' => Carbon::now()->addDays(90), // Asignamos una fecha de vencimiento 30 días después
            'email_farmacia' => $data['emailFarmacia'] ?? null,
            'celular_farmacia' => $data['celularFarmacia'] ?? null,
            'email_estudio' => $data['emailEstudio'] ?? null,
            'telefono_Estudio' => $data['telefonoEstudio'] ?? null,
            // 'tramite_finalizado' => $data['estadoTramite'],
            'id_tipo_movimiento' => $data['tipoMovimiento']['codTipoMovimiento'] ?? null,
            'id_institucion' => $data['instituciones']['idInstitucion'] ?? null,
            'derivado' => $data['derivado'] ?? null,
            'total_deuda_ospf' => $data['totalDeudaObraSocial'] ?? null,
            'observaciones' => $data['observaciones'] ?? null,
            'id_usuario' => $user->cod_usuario ,
            'localidad' => $data['localidad'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'nombre_usuario' => $user->nombre_apellidos ,
            'numero_registro' => $data['numeroRegistro'] ?? null,
            'id_empresa' => $data['empresa']['idEmpresa'] ?? null,
            'estado' => $data['estado'] ?? null,
            'id_expediente' => $data['expediente']['idExpediente'] ?? null,
            
        ];
        
        if (empty($data['codIntimacion'])) {
            Intimacion::create($payload);
            $msg = 'Intimación registrada correctamente';
        } else {
            $intimacion = Intimacion::where('id_intimacion', $data['codIntimacion'])->first();
            
            if (!$intimacion) {
                return response()->json(['message' => 'Intimación no encontrada'], 404);
            }
            
            $intimacion->update($payload);
            $msg = 'Intimación actualizada correctamente';
        }
        
        return response()->json(['message' => $msg], 200);
    }


    public function buscarSeguimientoIntimacion(Request $request)
    {
        $idEmpresa = $request->input('empresa');
        $usuario = $request->input('usuario');
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $query = Intimacion::query();

        $user = Auth::user();

        if ($user->perfil->nombre_perfil !== 'Administrador') {
            return response()->json(['error' => 'Debe seleccionar una empresa'], 403);
        }

        // Filtros por fecha (individuales o combinados)
        if (!empty($desde)) {
            $fechaDesde = Carbon::parse($desde)->startOfDay();
            $query->where('fecha_inicio_gestion', '>=', $fechaDesde);
        }

        if (!empty($hasta)) {
            $fechaHasta = Carbon::parse($hasta)->endOfDay();
            $query->where('fecha_inicio_gestion', '<=', $fechaHasta);
        }

        // Filtro por empresa si está presente
        if (!empty($idEmpresa)) {
            $query->where('id_empresa', $idEmpresa);
        }

        if ($usuario) {
            $query->where('nombre_usuario', 'like', '%' . $usuario . '%');
        }

        $query->orderBy('fecha_inicio_gestion', 'desc');

        // Cargar relaciones necesarias
        $intimaciones = $query->with(['empresa', 'tipoMovimiento', 'institucion','expediente.periodos'])->get();

        // Formato camelCase para el frontend
        $resultado = $intimaciones->map(function ($item) {
            return [
                'numeroRegistro'          => $item->numero_registro,
                'codIntimacion'           => $item->id_intimacion,
                'fechaRegistra'           => $item->fecha_inicio_gestion,
                'fechaVencimientoGestion' => $item->fecha_vencimiento_gestion,
                'totalDeuda'              => $item->total_deuda_ospf,
                'tramite_finalizado'      => $item->tramite_finalizado,
                'nombreUsuario'           => $item->nombre_usuario,
                'estado'                  => $item->estado,
                'empresa' => [
                    'cuit'        => $item->empresa->cuit ?? null,
                    'razonSocial' => $item->empresa->razon_social ?? null,
                ],
                'tipoMovimiento' => [
                    'descripcion'        => $item->tipoMovimiento->descripcion ?? null,
                    'codTipoMovimiento'  => $item->tipoMovimiento->id_tipo_movimiento ?? null,
                ],
                'instituciones' => [
                    'descripcion' => $item->instituciones->descripcion ?? null,
                ],
                'expediente' => [
                    'numeroExpediente' => $item->expediente->numero_expediente ?? null,
                    'montoTotalExpediente' => $item->expediente && $item->expediente->periodos
                        ? $item->expediente->periodos->sum('monto_deuda')
                        : 0
                ],
            ];
        });

        return response()->json($resultado);
    }


    public function getIntimacionById(Request $request)
    {
        $id = $request->query('id');

        $item = Intimacion::with(['empresa', 'tipoMovimiento', 'institucion'])->find($id);

        if (!$item) {
            return response()->json(['error' => 'Intimación no encontrada'], 404);
        }

        $resultado = [
            'numeroRegistro'         => $item->numero_registro,
            'codIntimacion'          => $item->id_intimacion,
            'empresa' => [
                'idEmpresa'        => $item->empresa->id_empresa,
                'cuit'        => $item->empresa->cuit ?? null,
                'razonSocial' => $item->empresa->razon_social ?? null,
                'nombreFantasia' => $item->empresa->nombre_fantasia ?? null,
            ],
            'atendidoPor'          => $item->atendido_por,
            'sector'     => $item->sector,
            'telefonoFarmacia'     => $item->telefono_farmacia,
            'fechaRegistra'          => $item->fecha_inicio_gestion,
            'fechaVencimientoGestion'=> $item->fecha_vencimiento_gestion,
            'emailFarmacia'             => $item->email_farmacia,
            'celularFarmacia'             => $item->celular_farmacia,
            'emailEstudio'             => $item->email_estudio,
            'telefonoEstudio'             => $item->telefono_Estudio,
            // 'estadoTramite'     => $item->tramite_finalizado,
            'estado'     => $item->estado,
            'tipoMovimiento' => [
                'descripcion' => $item->tipoMovimiento->descripcion ?? null,
                'codTipoMovimiento' => $item->tipoMovimiento->id_tipo_movimiento ?? null,
            ],
            'instituciones' => [
                'descripcion' => $item->institucion->descripcion ?? null,
                'idInstitucion' => $item->institucion->id_institucion ?? null,
            ],
            'derivado'             => $item->derivado,
            'totalDeudaObraSocial'             => $item->total_deuda_ospf,
            'observaciones'             => $item->observaciones,
            'idUsuario'             => $item->id_usuario,
            'localidad'             => $item->localidad,
            'direccion'             => $item->direccion,
            'nombreUsuario'          => $item->nombre_usuario,
            'numeroRegistro'          => $item->numero_registro,
            'idExpediente'          => $item->id_expediente,
        ];

        return response()->json($resultado, 200);
    }


    public function eliminarIntimacion($id)
    {
        try {
            $intimacion = Intimacion::findOrFail($id);

            // Marcamos la intimacion como Eliminado
            $intimacion->estado = 'Eliminado';
            $intimacion->save();
            

            return response()->json([
                'message' => 'Intimacion eliminada correctamente.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la Intimacion.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}