<?php

namespace App\Http\Controllers\Afiliados\Services;

use App\Http\Controllers\afiliados\repository\AfiliadoApiRepository;
use App\Http\Controllers\afiliados\repository\AfiliadoFatfaRepository;
use App\Http\Controllers\afiliados\repository\PadronAfiliadoRepository;
use App\Imports\PadronAfiliadoImport;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;

class ApiOspfPadronController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getObtenerAfiliado', 'getImportarAfiliados', 'getCruzarPadron', 'getCrearCuentasUsuarios']]);
    }

    public function getObtenerAfiliado(Request $request, AfiliadoApiRepository $api, AfiliadoFatfaRepository $repoFatfa)
    {
        $jwt = '$2a$12$ZlfXyaKmdl2cNKfo3wVmZuAbbUpLNYocLhHaegliv8v0EZCdFwBS2';

        if (is_null($request->bycript) || $request->bycript != $jwt) {
            return response([
                "message" => "OSPF requiere un bycript asignado para continuar",
                "status" => 401
            ], 401);
        }

        if (!isset($request->dni) || !is_string($request->dni) || !preg_match('/^\d{8,11}$/', $request->dni)) {
            return response()->json(['error' => 'El campo DNI debe ser un string con exactamente 8 números.'], 422);
        }

        $persona = $api->findTop1ByDni($request->dni);
        if ($persona != null) {
            if ($persona->verificado == '0') {
                $persona->convenio = $repoFatfa->findByExistsConvenioFatfa($persona->codigo, $persona->codigo2);
            }
        } else {
            return response()->json(['message' => "No se encontro datos para el numero de documento: {$request->dni}"], 404);
        }
        return response()->json($persona, 200);
    }

    public function getImportarAfiliados(Request $request)
    {
        $archivo = $request->file('archivo');
        if ($archivo) {
            try {
                DB::beginTransaction();
                $importacion = new PadronAfiliadoImport();
                Excel::import($importacion, $archivo);
                DB::commit();

                return response()->json(['message' => "Padron importado con éxito"], 200);
            } catch (\Throwable $exception) {
                DB::rollBack();
                return response()->json(['message' => $exception->getMessage()], 500);
            }
        } else {
            return response()->json(['message' => 'No se encontro ningun archivo'], 500);
        }
    }

    public function getCruzarPadron(Request $request, PadronAfiliadoRepository $padronPrincipal, AfiliadoApiRepository $padronExterno)
    {
        try {
            DB::beginTransaction();
            $dataExterna = $padronExterno->findByListAll($request->top);

            foreach ($dataExterna as $key) {
                if ($padronPrincipal->findByExistsDni($key->dni)) {
                    // SI EXISTE SE ACTUALIZA
                    $padronPrincipal->findByUpdateFlash(
                        $key->dni,
                        $key->sexo,
                        ($key->estado_civil == '99' ? '00' : '0' . $key->estado_civil),
                        $key->fecha_nacimiento,
                        $key->activo,
                        ($key->id_parentesco == '99' ? $key->id_parentesco : '0' . $key->id_parentesco)
                    );
                } else {
                    // SI NO SE CREA
                    $padronPrincipal->findByCreate($key);
                }
                $padronExterno->findByIsMacheo($key->id_padron);
            }

            DB::commit();

            return response()->json(['message' => "Padron cruzado con éxito"], 200);
        } catch (\Throwable $exception) {
            DB::rollBack();
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    public function getCrearCuentasUsuarios(Request $request, AfiliadoApiRepository $padronExterno)
    {
        try {
            DB::beginTransaction();
            $dataExterna = $padronExterno->findByListAfiliadosPrincipales($request->top);

            foreach ($dataExterna as $key) {
                if (!$padronExterno->findByCuenta($key->dni)) {
                    // SI NO EXISTE SE CREA CUENTA
                    $padronExterno->findByCrearCuenta($key, 'Ospf2025');
                    $padronExterno->findByIsMacheo($key->id_padron);
                }
            }

            DB::commit();

            return response()->json(['message' => "Padron cruzado con éxito"], 200);
        } catch (\Throwable $exception) {
            DB::rollBack();
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }
}
