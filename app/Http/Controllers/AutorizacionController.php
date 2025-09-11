<?php

namespace App\Http\Controllers;

use App\Models\afiliado\AfiliadoPadronEntity;
use App\Models\AutorizacionDetalleModel;
use App\Models\AutorizacionModels;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AutorizacionController extends Controller
{
    //
    public function getAutorizacion()
    {
        $query =  AutorizacionModels::with(['padron'])->get();
        return response()->json($query, 200);
    }

    public function filterAutorizacion($id)
    {
        $query = AutorizacionModels::with(['padron','detalles'])->where('id_autorizacion', $id)->first();
        $query->url = asset('/storage/Autorizacion/' . $query->url);
        return response()->json($query, 200);
    }

    public function LikefilterAutorizacion($id)
    {
        return AutorizacionModels::with(['padron'])->where('cuit', 'LIKE', "$id%")
            ->orWhere('razon_social', 'LIKE', "$id%")->get();
    }

    public function getFechaAutorizacion(Request $request)
    {
        $query = AutorizacionModels::whereBetween('fecha_pedido', [$request->desde, $request->hasta])->get();
        return response()->json($query, 200);
    }

    public function saveAutorizacion(Request $request)
    {
        //date_default_timezone_set('America/Lima');
        $now = new \DateTime();
        $now->setTimezone(new \DateTimeZone('America/Argentina/Buenos_Aires'));
        $datos = json_decode($request->input('json'));
        $nombreImagen=null;
        if ($datos->id_autorizacion != '') {
            $query = AutorizacionModels::where('id_autorizacion', $datos->id_autorizacion)->first();
            if($query->url_receta!=null){
                $nombreImagen=$query->url_receta;
            }
            
            if ($request->file('file')) {
                $imagen = $request->file('file');
                $nombreImagen = time() . '.' . $imagen->extension();
                $imagen->storeAs('Autorizacion', $nombreImagen, 'public');
            }
            $user = Auth::user();
            $query->fecha_autorizacion = $now->format('Y-m-d H:i:s');
            $query->estado = $datos->estado;
            $query->motivo = $datos->motivo;
            $query->observaciones = $datos->observaciones;
            $query->observacion_auditoria = $datos->observacion_auditoria;
            $query->id_usuario =  $user->cod_usuario;
            $query->url_receta = $nombreImagen;
            $query->save();
            
            if(isset($datos->detalle) && count($datos->detalle) > 0){                
                AutorizacionDetalleModel::where('id_autorizacion', $datos->id_autorizacion)->delete();
                $detalle=$datos->detalle;
                foreach ($detalle as $key) {
                    AutorizacionDetalleModel::create([
                        'cantidad_solicitada' => $key->cantidad_solicitada,
                        'cantidad_autorizada' =>$key->cantidad_autorizada,
                        'precio_unitario' => $key->precio_unitario,
                        'monto_pagar' => $key->monto_pagar,
                        'cod_tipo_practica' => $key->cod_tipo_practica,
                        'id_autorizacion' => $query->id_autorizacion,
                        'codigo_valor_practica'=>$key->codigo_valor_practica,
                        'cod_prestacion'=>$key->cod_prestacion,
                    ]);
                }
            }
            
            return response()->json(['message' => 'Solicitud actualizado correctamente'], 200);
        } else {
            if ($request->file('file')) {
                $imagen = $request->file('file');
                $nombreImagen = time() . '.' . $imagen->extension();
                $imagen->storeAs('Autorizacion', $nombreImagen, 'public');
            }
            AutorizacionModels::create([
                'tipo_autorizacion' => $datos->tipo_autorizacion,
                'fecha_autorizacion' => $datos->fecha_autorizacion,
                'fecha_pedido' => $now->format('Y-m-d H:i:s'),
                'dni' => $datos->dni,
                'estado' => $datos->estado,
                'motivo' => $datos->motivo,
                'observaciones' => $datos->observaciones,
                'url' => $nombreImagen,
                'cuil_tit'=>$datos->cuil_tit,
                'id_usuario' => $datos->id_usuario,
            ]);
            return response()->json(['message' => 'Datos de Solicitud registrados correctamente'], 200);
        }
    }

    public function updateEstado(Request $request)
    {
        AutorizacionModels::where('id_autorizacion', $request->id)->update(['activo' => $request->activo, 'observacion_auditoria' => $request->observacion_auditoria]);
        return response()->json(['message' => 'Solicitud cambiado correctamente'], 200);
    }

    public function getSolicitudesPorUsuario()
    {
        $user = Auth::user();
        $lista = [];
        $padron= AfiliadoPadronEntity::where('dni', $user->documento)->first();
        $query = AutorizacionModels::where('cuil_tit', $padron->cuil_tit)->get();
        $lista = $query->map(function($file) {
        $file->url = !empty($file->url) ? asset('storage/Autorizacion/' . $file->url) : null;
        $file->url_receta = !empty($file->url_receta) ? asset('storage/Autorizacion/' . $file->url_receta) : null;

            return $file;
        })->toArray();
        return response()->json($lista, 200);
    }
}
