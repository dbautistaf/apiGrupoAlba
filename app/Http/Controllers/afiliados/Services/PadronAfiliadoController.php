<?php

namespace App\Http\Controllers\Afiliados\Services;


use App\Http\Controllers\Afiliados\Dto\PersonaAfipDTO;
use App\Http\Controllers\Afiliados\Repository\PadronAfiliadoRepository;
use App\Models\Afiliado\AfiliadoPadronEntity;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class PadronAfiliadoController extends Controller
{

    public function getBuscarAfiliadoDNI(Request $request, PadronAfiliadoRepository $repository)
    {
        $afiliado = AfiliadoPadronEntity::where('dni', $request->dni)->first();
        $datosPersonales = null;
        if (strlen($request->dni) == 8 && (is_null($afiliado->fe_nac) || empty($afiliado->fe_nac) || is_null($afiliado->id_sexo) || empty($afiliado->id_sexo))) {
            $datosPersonales = $repository->findByBuscarAfiliadoAfip($request->dni);
            $afiliado->fe_nac = $datosPersonales->fechaNacimiento;
            $afiliado->id_sexo = $datosPersonales->sexo;
            $afiliado->update();
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
                $afiliado->id_sexo,
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
