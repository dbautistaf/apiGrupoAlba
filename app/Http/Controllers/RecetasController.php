<?php

namespace App\Http\Controllers;

use App\Exports\RecetasExport;
use App\Exports\TodoRecetasExport;
use App\Http\Controllers\Recetarios\Repository\RecetarioRepository;
use App\Models\DetalleRecetasModelo;
use App\Models\FileRecetas;
use App\Models\RecetasModelo;
use App\Models\User;
use App\Models\vademecumModelo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RecetasController extends Controller
{
    public function getListRecetas(Request $request)
    {
        if ($request->id != '') {
            $query = RecetasModelo::with('Afiliado', 'Farmacia', 'detalleReceta.vademecum')
                ->where('id_receta', $request->id)
                ->first();
            $detalle_doc = FileRecetas::where('id_receta', $query->id_receta)->get();
            foreach ($detalle_doc as $id_detalle) {
                array_push($arrayDoc, [
                    'id_file' => $id_detalle->id_file,
                    'nombre_file' => $id_detalle->nombre_file,
                    'url_archivo' => url('/storage/recetas/' . $id_detalle->nombre_file),
                    'fecha_proceso' => $id_detalle->fecha_proceso
                ]);
            }
            $query['detalle_doc'] = $arrayDoc;
        } else {
            $query = RecetasModelo::with('Afiliado', 'Farmacia', 'detalleReceta.vademecum')
                ->limit(400)
                ->get();
        }
        return response()->json($query, 200);
    }

    public function getUltimoRegistro()
    {
        $ultimoRegistro = RecetasModelo::latest('id_receta')->first();
        return response()->json($ultimoRegistro, 200);
    }

    public function getFechaRecetas(Request $request)
    {

        if ($request->idUser != '') {
            $query = RecetasModelo::with('Afiliado', 'Farmacia')->where('id_usuario', $request->idUser)
                ->whereBetween('fecha_carga', [$request->desde, $request->hasta])
                ->get();
        } else {
            $query = RecetasModelo::with('Afiliado', 'Farmacia')->whereBetween('fecha_carga', [$request->desde, $request->hasta])->get();
        }
        return response()->json($query, 200);
    }

    public function getfilterReceta($datos)
    {
        $query = RecetasModelo::with('Afiliado', 'Farmacia')
            ->where(function ($query) use ($datos) {
                $query->whereHas('Afiliado', function ($queryAfiliado) use ($datos) {
                    $queryAfiliado->where('nombre', 'LIKE', "$datos%")->orWhere('dni', 'LIKE', "$datos%");
                })->orWhereHas('Farmacia', function ($queryFarmacia) use ($datos) {
                    $queryFarmacia->where('razon_social', 'LIKE', "$datos%");
                });
            })
            ->orWhere('colegio', 'LIKE', "$datos%")->orWhere('numero_receta', 'LIKE', "$datos%")->get();
        return response()->json($query, 200);
    }

    public function getListarRecetarios(RecetarioRepository $repo, Request $request)
    {
        $data = [];
        if (is_null($request->persona) && !is_null($request->search)) {
            $data = $repo->findByListFarmaciaAndFecha($request->search, $request->desde, $request->hasta);
        } else if (!is_null($request->persona) && !is_null($request->search)) {
            $data = $repo->findByListFarmaciaAndFechaAndUsario($request->search, $request->desde, $request->hasta, $request->persona);
        } else if (!is_null($request->persona) && is_null($request->search)) {
            $data = $repo->findByListIdUsuarioAndFechaBetweenAndLimit($request->persona, $request->desde, $request->hasta);
        } else {
            $data = $repo->findByListLimit($request->desde, $request->hasta, 300);
        }

        return response()->json($data);
    }

    public function getBuscramedicamentoVademecum($nombre)
    {
        $query = vademecumModelo::where('nombre', 'LIKE', "$nombre%")
            ->orWhere('troquel', 'LIKE', "$nombre%")->get();
        return response()->json($query, 200);
    }

    public function filtrarMedico($datos)
    {
        $query = RecetasModelo::select('medico', 'matricula')->where('medico', 'LIKE', "%$datos%")
            ->orWhere('matricula', 'LIKE', "%$datos%")->groupBy('medico', 'matricula')->get();
        return response()->json($query, 200);
    }

    public function filtrarRecetasUsuario($iduser)
    {
        $user = Auth::user();
        if ($user->cod_usuario == 2) {
            $query = RecetasModelo::with('Afiliado', 'Farmacia', 'detalleReceta.vademecum')->where('id_usuario', $iduser)->get();
            return response()->json($query, 200);
        } else {
            return response()->json(['message' => 'No Tiene permisos para realizar esta operación'], 500);
        }
    }

    public function postSaveRecetas(Request $request)
    {

        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        $user = Auth::user();
        $array = json_decode($request->input('array'));
        $receta = json_decode($request->input('receta'));
        //return response()->json($receta->fecha_carga, 200);
        if (isset($receta->id_receta) && $receta->id_receta !== '') {
            try {
                DB::beginTransaction();
                $query = RecetasModelo::where('id_receta', $receta->id_receta)->first();
                if ($receta->id_usuario == $query->id_usuario) {
                    $query->numero_receta = $receta->numero_receta;
                    $query->id_farmacia = $receta->id_farmacia;
                    $query->id_padron = $receta->id_padron;
                    $query->observaciones = $receta->observaciones;
                    $query->fecha_receta = $receta->fecha_receta;
                    $query->fecha_carga = $receta->fecha_carga;
                    $query->periodo = $receta->periodo;
                    $query->medico = $receta->medico;
                    $query->caratula = $receta->caratula;
                    $query->matricula = $receta->matricula;
                    $query->colegio = $receta->colegio;
                    $query->origen = $receta->origen;
                    $query->subtotal = '0.00';
                    $query->importe_total = $receta->importe_total;
                    $query->total_afiliado = $receta->total_afiliado;
                    $query->total_obra_social = $receta->total_obra_social;
                    $query->id_tipo_plan = $receta->id_tipo_plan;
                    $query->fecha_prescripcion = $receta->fecha_prescripcion;
                    //$query->id_usuario = $query->id_usuario;
                    $query->lote = $receta->lote;
                    $query->validado = $receta->validado;
                    $query->numero_validacion = $receta->numero_validacion;
                    $query->save();

                    foreach ($array as $datos) {
                        if ($datos->id_detalle_receta != '') {
                            $detalle = DetalleRecetasModelo::where('id_detalle_receta', $datos->id_detalle_receta)->first();
                            $detalle->id_vademecum = $datos->id_vademecum;
                            $detalle->cantidad = $datos->cantidad;
                            $detalle->valor_unitario = $datos->valor_unitario;
                            $detalle->valor_total = $datos->valor_total;
                            $detalle->afiliado_total = $datos->afiliado_total;
                            $detalle->venta_publico = $datos->venta_publico;
                            $detalle->cargo_osyc = $datos->cargo_osyc;
                            $detalle->diabetes = $datos->diabetes;
                            $detalle->recupero = $datos->recupero;
                            $detalle->pmi = $datos->pmi;
                            $detalle->id_receta = $receta->id_receta;
                            $detalle->id_cobertura = $datos->id_cobertura;
                            $detalle->save();
                        } else {
                            DetalleRecetasModelo::create([
                                'id_vademecum' => $datos->id_vademecum,
                                'cantidad' => $datos->cantidad,
                                'valor_unitario' => $datos->valor_unitario,
                                'valor_total' => $datos->valor_total,
                                'afiliado_total' => $datos->afiliado_total,
                                'venta_publico' => $datos->venta_publico,
                                'cargo_osyc' => $datos->cargo_osyc,
                                'diabetes' => $datos->diabetes,
                                'recupero' => $datos->recupero,
                                'pmi' => $datos->pmi,
                                'id_receta' => $receta->id_receta,
                                'id_cobertura' => $datos->id_cobertura,
                            ]);
                        }
                    }

                    if ($request->hasFile('file')) {
                        foreach ($request->file('file') as $archivo) {
                            $fileName = time() . '.' . $archivo->extension();
                            $archivo->storeAs('recetas', $fileName, 'public');
                            FileRecetas::create([
                                'nombre_file' => $fileName,
                                'id_receta' => $receta->id_receta,
                                'fecha_proceso' => $fechaActual,
                            ]);
                        }
                    }
                    DB::commit();
                    return response()->json(['message' => 'Receta medica actualizado correctamente'], 200);
                } else {
                    return response()->json(['message' => 'No tiene permitido actualizar la receta medica'], 500);
                }
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json(['message' => $th->getMessage()], 500);
            }
        } else {
            try {
                DB::beginTransaction();
                $newreceta = RecetasModelo::create([
                    'numero_receta' => $receta->numero_receta,
                    'id_farmacia' => $receta->id_farmacia,
                    'id_padron' => $receta->id_padron,
                    'observaciones' => $receta->observaciones,
                    'fecha_receta' => $receta->fecha_receta,
                    'fecha_carga' => $receta->fecha_carga,
                    'periodo' => $receta->periodo,
                    'medico' => $receta->medico,
                    'caratula' => $receta->caratula,
                    'matricula' => $receta->matricula,
                    'colegio' => $receta->colegio,
                    'origen' => $receta->origen,
                    'subtotal' => '0.00',
                    'importe_total' => $receta->importe_total,
                    'total_afiliado' => $receta->total_afiliado,
                    'total_obra_social' => $receta->total_obra_social,
                    'id_tipo_plan' => $receta->id_tipo_plan,
                    'fecha_prescripcion' => $receta->fecha_prescripcion,
                    'id_usuario' => $user->cod_usuario,
                    'lote' => $receta->lote,
                    'validado' => $receta->validado,
                    'numero_validacion' => $receta->numero_validacion
                ]);
                foreach ($array as $datos) {
                    DetalleRecetasModelo::create([
                        'id_vademecum' => $datos->id_vademecum,
                        'cantidad' => $datos->cantidad,
                        'valor_unitario' => $datos->valor_unitario,
                        'valor_total' => $datos->valor_total,
                        'afiliado_total' => $datos->afiliado_total,
                        'venta_publico' => $receta->venta_publico,
                        'cargo_osyc' => $datos->cargo_osyc,
                        'diabetes' => $datos->diabetes,
                        'recupero' => $datos->recupero,
                        'pmi' => $datos->pmi,
                        'id_receta' => $newreceta->id_receta,
                        'id_cobertura' => $datos->id_cobertura,
                    ]);
                }
                if ($request->hasFile('file')) {
                    foreach ($request->file('file') as $archivo) {
                        $fileName = time() . '.' . $archivo->extension();
                        $archivo->storeAs('recetas', $fileName, 'public');
                        FileRecetas::create([
                            'nombre_file' => $fileName,
                            'id_receta' => $newreceta->id_receta,
                            'fecha_proceso' => $fechaActual,
                        ]);
                    }
                }
                DB::commit();
                return response()->json(['message' => 'Receta medica registrada correctamente'], 200);
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack();
                return response()->json(['message' => $th->getMessage()], 500);
            }
        }
    }

    public function postdeleteRecetas(Request $request)
    {
        $user = Auth::user();
        if ($user->perfil->nombre_perfil == 'Administrador') {
            RecetasModelo::where('id_receta', $request->id_receta)->delete();
            return response()->json(['message' => 'Receta medica eliminado correctamente'], 200);
        } else {
            return response()->json(['message' => 'No tiene permisos para realizar esta acción'], 500);
        }
    }



    public function postSaveVademecum(Request $request)
    {
        if ($request->id_vademecum != '') {
        } else {
            vademecumModelo::create([
                'troquel' => $request->troquel,
                'registro' => $request->registro,
                'nombre' => $request->nombre,
                'presentacion' => $request->presentacion,
                'laboratorio' => $request->laboratorio,
                'droga' => $request->droga,
                'accion' => $request->accion,
                'acargo_ospf' => $request->acargo_ospf,
                'autorizacion_previa' => $request->autorizacion_previa,
                'activo' => $request->activo,
            ]);
            return response()->json(['message' => 'Medicamento registrado correctamente'], 200);
        }
    }

    public function getExportRecetas(Request $request)
    {

        if ($request->desde != '' && $request->hasta) {
            $desde = $request->desde;
            $hasta = $request->hasta;
            return Excel::download(new RecetasExport($desde, $hasta), 'recetas.xlsx');
        } else {
            return Excel::download(new TodoRecetasExport, 'recetas.xlsx');
        }
    }

    public function getListuser()
    {
        $user = Auth::user();
        if ($user->cod_perfil == 2) {
            $listUser = User::get();
            return response()->json($listUser, 200);
        }
    }

    public function getListarRecetariosAfiliado(RecetarioRepository $repo, Request $request)
    {
        return response()->json($repo->findByListRecetariosAfiliado($request->dni));
    }
}
