<?php

namespace App\Http\Controllers\afiliados;

use App\Http\Controllers\afiliados\dto\PersonaAfipDTO;
use App\Http\Controllers\afiliados\repository\PadronAfiliadoRepository;
use App\Models\afiliado\AfiliadoPadronEntity;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class PadronAfiliadoController extends Controller
{

    public function getBuscarAfiliadoDNI(Request $request, PadronAfiliadoRepository $repository)
    {
        $afiliado = AfiliadoPadronEntity::where('dni', $request->dni)->first();
        $datosPersonales = null;
        if (strlen($request->dni) == 8 && (is_null($afiliado->fe_nac) || empty($afiliado->fe_nac))) {
            $datosPersonales = $repository->findByBuscarAfiliadoAfip($request->dni);
            if (is_null($datosPersonales)) {
                $afiliado->fe_nac = $datosPersonales->fechaNacimiento ?? null;
                $afiliado->update();
            }

        } else {
            $fechaNacimiento = Carbon::parse($afiliado->fe_nac);
            $edad = $fechaNacimiento->age;
            $datosPersonales = new PersonaAfipDTO(
                $afiliado->apellidos,
                $afiliado->calle,
                '',
                $afiliado->id_cpostal,
                $afiliado->cuil_benef,
                '',
                $afiliado->fe_nac,
                '',
                '',
                $afiliado->nombre,
                $afiliado->dni,
                '',
                '',
                '',
                $edad
            );
        }

        return response()->json($datosPersonales);
    }

    public function getPadronAfiliados(PadronAfiliadoRepository $repository, Request $request)
    {
        $datosPersonales = [];
        if (!empty($request->search)) {
            if (is_numeric($request->search)) {
                $datosPersonales = $repository->findByListDniLike($request->search);
            } else {
                $datosPersonales = $repository->findByListApellidosNombresLike($request->search);
            }
        } else {
            $datosPersonales = $repository->findByListPaginate(100);
        }
        return response()->json($datosPersonales);
    }
}
