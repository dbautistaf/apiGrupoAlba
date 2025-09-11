<?php

namespace App\Http\Controllers\Afiliados\Services;

use App\Models\afiliado\AfiliadoCronicoEntity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AfiliadoCronicoController extends Controller
{
    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }


    public function getListCronicos(Request $request)
    {
        if ($request->id != '') {
            $query = AfiliadoCronicoEntity::where('id_padron', $request->id)->first();
        } else {
            $query = AfiliadoCronicoEntity::get();
        }
        return response()->json($query, 200);
    }

    public function postSaveCronicos(Request $request)
    {

        if ($request->id_cronico != '') {
            $query = AfiliadoCronicoEntity::where('id_cronico', $request->id_cronico)->first();
            $query->id_patologia = $request->id_patologia;
            $query->observaciones = $request->observaciones;
            $query->fecha_alta = $request->fecha_alta;
            $query->fecha_baja = $request->fecha_baja;
            $query->fecha_carga = $request->fecha_carga;
            $query->id_padron = $request->id_padron;
            $query->fecha_modifica = $this->fechaActual;
            $query->cod_usuario_modifica = $this->user->cod_usuario;

            $query->save();
            $msg = 'datos de afiliado cronico actualizado correctamente';
        } else {
            $cronicos = AfiliadoCronicoEntity::where('id_padron', $request->id_padron)->first();
            if ($cronicos) {
                return response()->json(['message' => 'El Afiliado ya se encuentra registrado como afiliado cronico'], 500);
            }

            AfiliadoCronicoEntity::create([
                'id_patologia' => $request->id_patologia,
                'observaciones' => $request->observaciones,
                'fecha_alta' => $request->fecha_alta,
                'fecha_baja' => $request->fecha_baja,
                'fecha_carga' => $this->fechaActual,
                'id_usuario' => $this->user->cod_usuario,
                'id_padron' => $request->id_padron
            ]);
            $msg = 'datos de afiliado cronico registrados correctamente';
        }
        return response()->json(['message' => $msg], 200);
    }

    public function getListar(Request $request)
    {
        $data = null;
        $sql = AfiliadoCronicoEntity::with(['afiliado', 'patologia']);
        if (!is_null($request->searchs)) {
            $sql->whereHas('afiliado', function ($subQuery) use ($request) {
                $subQuery->where('dni', 'LIKE', "%$request->searchs%")
                    ->orWhere('apellidos', 'LIKE', "%$request->searchs%");
            });
        }

        if (!is_null($request->desde) && !is_null($request->hasta)) {
            $sql->whereBetween('fecha_baja', [$request->desde, $request->hasta]);
        }

        if (!is_null($request->id_patologia)) {
            $sql->where('id_patologia', $request->id_patologia);
        }

        $data = $sql->get();

        return response()->json($data);
    }

    public function getBuscarId(Request $request)
    {
        return response()->json(AfiliadoCronicoEntity::with(['afiliado'])->find($request->id));
    }
}
