<?php

namespace App\Http\Controllers\prestadores;

use App\Http\Controllers\prestadores\repository\ImputacionesPrestadoreRepository;
use App\Http\Controllers\prestadores\repository\PadronPrestadoresRepository;
use App\Http\Controllers\prestadores\repository\PrestadorRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class  PrestadoresController extends Controller
{
    public static function srvFilterDataPadronPrestador(PadronPrestadoresRepository $repository, Request $request)
    {
        $data = [];

        if (is_numeric($request->search)) {
            $data = $repository->findByListCuitLike($request->search, 100);
        } else if (!is_null($request->search)) {
            $data = $repository->findByListRazonSocialLike(($request->search), 100);
        } else {
            $data = $repository->findByListPaginateTop(100);
        }

        return response()->json($data, 200);
    }

    public function postProcesarPrestadorFlash(PrestadorRepository $repo, Request $request)
    {
        return response()->json($repo->findBySaveFlash($request), 200);
    }

    public function postRegistrarPrestador(PrestadorRepository $repo, Request $request, ImputacionesPrestadoreRepository $repImputacion)
    {
        DB::beginTransaction();
        try {

            if (!is_null($request->cod_prestador)) {
                $prestador = $repo->findByUpdatePrestador($request);

                if (is_null($request->datos_bancarios["cod_banco_empresa"])) {
                    $repo->findByCrearDatosBancarios($request->datos_bancarios, $prestador->cod_prestador, $prestador->cuit);
                } else {
                    $repo->findByUpdateDatosBancarios($request->datos_bancarios, $prestador->cod_prestador, $prestador->cuit);
                }

                if (is_null($request->metodo_pago["id_pago_proveedor"])) {
                    $repo->findByCrearMetodoPago($request->metodo_pago, $prestador->cod_prestador);
                } else {
                    $repo->findByUpdateMetodoPago($request->metodo_pago, $prestador->cod_prestador);
                }

                foreach ($request->detalle as $key) {
                    if (!is_null($key['id_imputacion_prestador'])) {
                        $repImputacion->findByUpdateImputaciones($key, $prestador->cod_prestador);
                    } else {
                        $repImputacion->findByAgregarImputaciones($key, $prestador->cod_prestador);
                    }
                }

                DB::commit();
                return response()->json(["message" => "Prestador actualizado correctamente."], 200);
            } else {

                // if (!$repo->findByExistCuit($request->cuit)) {
                $prestador = $repo->findByCrearPrestador($request);
                $repo->findByCrearDatosBancarios($request->datos_bancarios, $prestador->cod_prestador, $prestador->cuit);
                $repo->findByCrearMetodoPago($request->metodo_pago, $prestador->cod_prestador);

                foreach ($request->detalle as $key) {
                    $repImputacion->findByAgregarImputaciones($key, $prestador->cod_prestador);
                }

                DB::commit();
                return response()->json(["message" => "Prestador registrado correctamente."], 200);
                //} else {
                DB::rollBack();
                return response()->json([
                    'message' => "El N° de CUIT <b>" . $request->cuit . "</b>, ya se encuentra registrado en nuestro sistema."
                ], 409);
                //}
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getBuscarPrestadorId(PrestadorRepository $repo, $id)
    {
        return response()->json($repo->findById($id), 200);
    }

    public function getTipoImputacionContable(PadronPrestadoresRepository $repo, Request $request)
    {
        return response()->json($repo->findByListarTipoImputacion($request), 200);
    }
}
