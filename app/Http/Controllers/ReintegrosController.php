<?php

namespace App\Http\Controllers;

use App\Models\ReintegrosModelos;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ReintegrosController extends Controller
{
    //
    public function getReintegros()
    {
        $datos =  ReintegrosModelos::get();
        return response()->json($datos, 200);
    }

    public function getLikeReintegro(Request $request)
    {
        if ($request->datos == '') {
            $query =  ReintegrosModelos::with('Afiliado', 'Filial', 'Autorizacion')->get();
        } else {
            $datos = $request->datos;
            $query = ReintegrosModelos::with('Afiliado', 'Filial', 'Autorizacion')->where(function ($query) use ($datos) {
                $query->whereHas('Afiliado', function ($queryAfiliado) use ($datos) {
                    $queryAfiliado->where('nombre', 'LIKE', "$datos%")->orWhere('dni', 'LIKE', "$datos%")
                        ->orWhere('cuil_benef', 'LIKE', "$datos%");
                })->orWhereHas('Filial', function ($queryFilial) use ($datos) {
                    $queryFilial->where('filial', 'LIKE', "$datos%");
                });
            })->get();
        }

        return response()->json($query, 200);
    }

    public function getFechaReintegro(Request $request)
    {

        $query = ReintegrosModelos::with('Afiliado', 'Filial', 'Autorizacion')->whereBetween('fecha_carga', [$request->desde, $request->hasta])->get();

        return response()->json($query, 200);
    }

    public function filterReintegro($id)
    {
        return ReintegrosModelos::with('Afiliado', 'Filial', 'Autorizacion')->where('nro_reintegro', $id)->first();
    }

    public function saveReintegro(Request $request)
    {

        $nombreImagen = "No Image";
        $datos = json_decode($request->input('array'));
        if ($request->hasFile('file')) {
            $imagen = $request->file('file');
            $nombreImagen = time() . '.' . $imagen->extension();
            $imagen->storeAs('FileReintegro', $nombreImagen, 'public');
        }

        if ($datos->nro_reintegro != '') {

            $query = ReintegrosModelos::where('nro_reintegro', $datos->nro_reintegro)->first();
            $query->nro_reintegro = $datos->nro_reintegro;
            $query->fecha_solicitud = $datos->fecha_solicitud;
            $query->fecha_transferencia = $datos->fecha_transferencia;
            $query->url_adjunto = $nombreImagen;
            $query->motivo = $datos->motivo;
            $query->importe_solicitado = $datos->importe_solicitado;
            $query->importe_reconocido = $datos->importe_reconocido;
            $query->autorizado_por = $datos->autorizado_por;
            $query->observaciones = $datos->observaciones;
            $query->cbu_prestador = $datos->cbu_prestador;
            $query->nro_factura = $datos->nro_factura;
            $query->id_usuario = $datos->id_usuario;
            $query->id_filial = $datos->id_filial;
            $query->id_afiliados = $datos->id_afiliados;
            $query->fecha_carga = $datos->fecha_carga;
            $query->id_estado_autorizacion = $datos->id_estado_autorizacion;
            $query->nombre_prestador =$datos->nombre_prestador;
            $query->estado =$datos->estado;
            $query->cantidad = $datos->cantidad;
            $query->observaciones_auditoria = $datos->observaciones_auditoria;
            $query->save();
            return response()->json(['message' => 'Datos de reintegro actualizado correctamente'], 200);
        } else {
            $now = new \DateTime();
            $user = Auth::user();
            ReintegrosModelos::create([
                'nro_reintegro' => $datos->nro_reintegro,
                'fecha_solicitud' => $datos->fecha_solicitud,
                'fecha_transferencia' => $datos->fecha_transferencia,
                'url_adjunto' => $nombreImagen,
                'motivo' => $datos->motivo,
                'importe_solicitado' => $datos->importe_solicitado,
                'importe_reconocido' => $datos->importe_reconocido,
                'autorizado_por' => $datos->autorizado_por,
                'observaciones' => $datos->observaciones,
                'cbu_prestador' => $datos->cbu_prestador,
                'nro_factura' => $datos->nro_factura,
                'id_usuario' => $user->cod_usuario,
                'id_filial' => $datos->id_filial,
                'id_afiliados' => $datos->id_afiliados,
                'fecha_carga' => $now->format('Y-m-d'),
                'id_estado_autorizacion' => $datos->id_estado_autorizacion,
                'nombre_prestador'=> $datos->nombre_prestador,
                'estado'=> $datos->estado,
                'cantidad'=> $datos->cantidad,
                'observaciones_auditoria'=> $datos->observaciones_auditoria,
            ]);
            return response()->json(['message' => 'Datos de reintegro registrados correctamente'], 200);
        }
    }

    public function deleteReintegro(Request $request)
    {
        ReintegrosModelos::where('nro_reintegro', $request->id)->delete();
        return response()->json(['message' => 'Reintegro eliminado correctamente'], 200);
    }
}
