<?php

namespace App\Http\Controllers\Contabilidad\Services;

use App\Http\Controllers\Contabilidad\Repository\ImputacionProveedoresCuentaContableRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ImputacionProveedoresCuentaContableController extends Controller
{
    public function getListar(Request $request, ImputacionProveedoresCuentaContableRepository $repo)
    {
        return response()->json($repo->findByListar());
    }

    public function getListarConFiltros(Request $request, ImputacionProveedoresCuentaContableRepository $repo)
    {
        return response()->json($repo->findByListarConFiltros($request->all()));
    }

    public function getProcesar(Request $request, ImputacionProveedoresCuentaContableRepository $repo)
    {
        try {
            DB::beginTransaction();

            if (!is_null($request->id_imputacion_proveedor_cuenta_contable)) {
                $repo->findByUpdate($request, $request->id_imputacion_proveedor_cuenta_contable);
                DB::commit();
                return response()->json(['message' => 'Registro modificado con éxito.'], 200);
            }

            if ($repo->findByExisteRelacion($request->id_detalle_plan, $request->codigo_cuenta)) {
                DB::rollBack();
                return response()->json(['message' => 'Ya existe una relación con la cuenta y el código de imputación para el período vigente.'], 409);
            }

            $repo->findByCrear($request);
            DB::commit();
            return response()->json(['message' => 'Registro procesado con éxito.'], 200);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code'    => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getEditar($id, ImputacionProveedoresCuentaContableRepository $repo)
    {
        $registro = $repo->findById($id);
        if (!$registro) {
            return response()->json(['message' => 'Registro no encontrado.'], 404);
        }
        return response()->json($registro);
    }

    public function delete(Request $request, ImputacionProveedoresCuentaContableRepository $repo)
    {
        $id = $request->id ?? null;
        if (is_null($id)) {
            return response()->json(['message' => 'ID no proporcionado.'], 400);
        }

        try {
            DB::beginTransaction();
            $deleted = $repo->findByEliminar($id);
            DB::commit();

            if ($deleted) {
                return response()->json(['message' => 'Registro eliminado con éxito.'], 200);
            }
            return response()->json(['message' => 'Registro no encontrado o no eliminado.'], 404);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code'    => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
