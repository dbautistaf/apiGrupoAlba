<?php

namespace App\Http\Controllers\Pmi;

use App\Http\Controllers\Controller;
use App\Models\afiliado\AfiliadoPadronEntity;
use App\Models\Pmi\PmiModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Auth;

class pmiController extends RoutingController
{
    //

    public function srvFilterPadronFemenino(Request $request)
    {

        $data = [];
        try {
            if (strlen($request->dni) === 8) {
                $data = AfiliadoPadronEntity::with(['certificado', 'detalleplan.TipoPlan', 'tipoParentesco'])->where('dni', $request->dni)->where('id_sexo', 'F')->get();
            } else if (strlen($request->dni) === 11) {
                $data = AfiliadoPadronEntity::with(['certificado', 'detalleplan.TipoPlan', 'tipoParentesco'])->where('cuil_benef', $request->dni)->where('id_sexo', 'F')->get();
            } else if (empty($request->dni)) {
                $data = AfiliadoPadronEntity::with(['certificado', 'detalleplan.TipoPlan', 'tipoParentesco'])->where('id_sexo', 'F')->orderBy('id')->limit(7)->get();
            } else {
                $data = AfiliadoPadronEntity::with(['certificado', 'detalleplan.TipoPlan', 'tipoParentesco'])
                    ->where('id_sexo', 'F')
                    ->where('dni', 'like', '%' . $request->dni . '%')
                    ->orWhere('cuil_benef', 'like', '%' . $request->dni . '%')
                    ->orderBy('id')
                    ->limit(7)
                    ->get();
            }
            $data = $data->map(function ($afiliado) {
                $afiliado->edad = \Carbon\Carbon::parse($afiliado->fe_nac)->age;
                return $afiliado;
            });

            if (count($data) == 0) {
                return response()->json(["message" => "No se encontro resultados para <b>" . $request->dni . "</b>"], 404);
            }

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getPmi(Request $request)
    {
        $query = '';
        $dni = $request->dni;
        if ($dni == '') {
            $query =  PmiModel::with(['afiliado', 'embarazo'])->get();
        } else {
            $query =  PmiModel::with(['afiliado', 'embarazo'])->where(function ($query) use ($dni) {
                $query->whereHas('afiliado', function ($queryAfiliado) use ($dni) {
                    $queryAfiliado->where('nombre', 'LIKE', "$dni%")->orWhere('dni', 'LIKE', "$dni%");
                });
            })->get();
        }

        return response()->json($query, 200);
    }

    public function postSavePmi(Request $request)
    {
        $datos = json_decode($request->input('json'));
        $now = new \DateTime('now', new \DateTimeZone('America/Argentina/Buenos_Aires'));
        $url = NULL;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $url = time() . '.' . $file->extension();
            $file->storeAs('pmi', $url, 'public');
        }

        if ($datos->id_pmi != '') {
            if ($url != NULL) {
                $datos->url_adjunto = $url;
            }
            $query = PmiModel::where('id_pmi', $datos->id_pmi)->first();

            $query->id_tipo_embarazo = $datos->id_tipo_embarazo;
            $query->observaciones = $datos->observaciones;
            $query->fecha_alta = $datos->fecha_alta;
            $query->fecha_baja = $datos->fecha_baja;
            $query->id_usuario = $datos->id_usuario;
            $query->dni_afiliado = $datos->dni_afiliado;
            $query->url_adjunto = $datos->url_adjunto;
            $query->save();
            return response()->json(['message' => 'PMI Actualizado correctamente'], 200);
        } else {
            $user = Auth::user();
            PmiModel::create([
                'id_tipo_embarazo' => $datos->id_tipo_embarazo,
                'observaciones' => $datos->observaciones,
                'fecha_alta' => $datos->fecha_alta,
                'fecha_baja' => $datos->fecha_baja,
                'fecha_carga' => $now->format('Y-m-d'),
                'id_usuario' => $user->cod_usuario,
                'dni_afiliado' => $datos->dni_afiliado,
                'url_adjunto' => $url
            ]);
            return response()->json(['message' => 'PMI Registrado correctamente'], 200);
        }
    }

    public function getSelectPmi(Request $request)
    {
        $pmi = PmiModel::where('id_pmi', $request->id)->first();
        if ($pmi->url_adjunto != NULL) {
            $pmi->url = url('/storage/pmi/' . $pmi->url_adjunto);
        }
        return response()->json($pmi, 200);
    }

    public function postdeletePmi(Request $request)
    {
        PmiModel::where('id_pmi', $request->id_pmi)->delete();
        return response()->json(['message' => 'Pmi eliminado correctamente'], 200);
    }
}
