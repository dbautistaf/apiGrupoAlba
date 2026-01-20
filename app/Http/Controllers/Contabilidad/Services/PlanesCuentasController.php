<?php

namespace App\Http\Controllers\Contabilidad\Services;

use App\Http\Controllers\Contabilidad\DTOs\PadronMapaPlanesDTOs;
use App\Http\Controllers\Contabilidad\Repository\PlanesCuentasRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class PlanesCuentasController extends Controller
{
    public function getListar(Request $request, PlanesCuentasRepository $repo)
    {
        return response()->json($repo->findByList($request));
    }

    public function getProcesar(Request $request, PlanesCuentasRepository $repo)
    {
        DB::beginTransaction();
        try {
            if (is_null($request->id_plan_cuenta)) {

                $repo->findByCreate($request);
                DB::commit();
                return response()->json(["message" => "El Plan se registro con éxito."], 200);
            } else {
                $repo->findByUpdate($request, $request->id_plan_cuenta);
                DB::commit();
                return response()->json(["message" => "El Plan se modifico con éxito."], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getId(Request $request, PlanesCuentasRepository $repo)
    {
        return response()->json($repo->findById($request->id));
    }

    public function getAgregarNivel(Request $request, PlanesCuentasRepository $repo)
    {
        if (!is_null($request->id_detalle_nivel)) {
            $repo->findByAgregarSubNivel($request);
        } else {
            $repo->findByAgregarNivel($request);
        }

        return response()->json(["message" => "Registro agregado con éxito"]);
    }

    public function getEliminarNivel(Request $request, PlanesCuentasRepository $repo)
    {
        $repo->findByDeleteNivel($request->id);
        return response()->json(["message" => "Registro eliminado con éxito"]);
    }

    public function getListarDetalleNiveles(Request $request, PlanesCuentasRepository $repo)
    {
        return response()->json($repo->findByListDetalleNiveles($request->id));
    }

    public function getListarMatrizPlanesCuenta(Request $request, PlanesCuentasRepository $repo)
    {
        $cuentaCabecera = $repo->findById($request->id_plan_cuenta);
        if (!$cuentaCabecera) {
            return response()->json(['error' => 'Cuenta no encontrada'], 404);
        }
        $padronCuenta = [];
        $main = new PadronMapaPlanesDTOs(
            $cuentaCabecera->plan_cuenta,
            true,
            'bx bxs-key',
            'bx bxs-lock-alt',
            [],
            null,
            null,
            $cuentaCabecera->id_plan_cuenta,
            null,
            null,
            'I',
            null,
            null,
            $cuentaCabecera->plan_cuenta,
            null
        );

        if ($repo->findByCountMatrizPlanCuenta($request->id_plan_cuenta) > 0) {
            //@OBTENEMOS LOS DATOS DE LOS GRUPOS (1)
            $dtListaNiveles = $repo->findByListDetalleNiveles($request->id_plan_cuenta);
            $dtListaGrupos = $repo->findByListDetallePlanesCuentaPorNivel($request->id_plan_cuenta, 'I');
            if (count($dtListaNiveles) > 0 && count($dtListaGrupos) > 0) {
                foreach ($dtListaGrupos as $keyGrupo) {
                    //@AGREGAMOS EL GRUPO
                    $rowGrupos = $repo->findByAddItem($keyGrupo, $keyGrupo->grupo, $keyGrupo->subgrupo, 'bx bxs-check-circle', 'GRUPO', $keyGrupo->tipo->descripcion);
                    //@OBTENEMOS LOS SUBGRUPOS (2)
                    $dtListaSubGrupos = $repo->findByListDetallePlanesCuentaPorSubNivel($request->id_plan_cuenta, $keyGrupo->id_detalle_plan, 1);
                    if (count($dtListaSubGrupos) > 0) {
                        $i = 0;
                        foreach ($dtListaSubGrupos as $keySubGrupo) {
                            //@AGREGAMOS EL SUBGRUPO
                            $rowGrupos->children[] = $repo->findByAddItem($keySubGrupo, $keySubGrupo->grupo, $keySubGrupo->subgrupo, 'bx bxs-check-circle', 'SUBGRUPO', $keySubGrupo->tipo->descripcion);
                            //@OBTENEMOS LAS CUENTAS (3)
                            $dtListaCuentas = $repo->findByListDetallePorSubNivel($request->id_plan_cuenta, 3, $keySubGrupo->id_detalle_plan, 2);
                            if (count($dtListaCuentas) > 0) {
                                $x = 0;
                                foreach ($dtListaCuentas as $keycuenta) {
                                    //@AGREGAMOS LA CUENTA
                                    $rowGrupos->children[$i]->children[] = $repo->findByAddItem($keycuenta, $keycuenta->grupo, $keycuenta->subgrupo, 'bx bxs-check-circle', 'CUENTA', $keycuenta->tipo->descripcion);
                                    //OBTENEMOS LAS SUBCUENTAS (4)
                                    $dtListaSubCuentas = $repo->findByListDetallePorSubNivel($request->id_plan_cuenta, 4, $keycuenta->id_detalle_plan, 3);
                                    if (count($dtListaSubCuentas) > 0) {
                                        $y = 0;
                                        foreach ($dtListaSubCuentas as $keySubCuentas) {
                                            //@AGREGAMOS LAS SUBCUENTAS
                                            $rowGrupos->children[$i]->children[$x]->children[] = $repo->findByAddItem($keySubCuentas, $keySubCuentas->grupo, $keySubCuentas->subgrupo, 'bx bxs-check-circle', 'SUBCUENTA', $keySubCuentas->tipo->descripcion);
                                            //@LISTAMOS LAS SUBCUENTAS1 (5) === SE ME COMPLICO ESTE MMMMM . ATTE J.MORE.I
                                            $dtListaSubCuentas1 = $repo->findByListDetallePorSubNivel($request->id_plan_cuenta, 5, $keySubCuentas->id_detalle_plan, 4);
                                            if (count($dtListaSubCuentas1) > 0) {
                                                foreach ($dtListaSubCuentas1 as $keysubCuentas1) {
                                                    //@AGREGAMOS LAS SUBCUENTAS1
                                                    $rowGrupos->children[$i]->children[$x]->children[$y]->children[] = $repo->findByAddItem($keysubCuentas1, $keysubCuentas1->grupo, $keysubCuentas1->subgrupo, 'bx bxs-check-circle', 'SUBCUENTA 1', $keysubCuentas1->tipo->descripcion);
                                                }
                                                $rowGrupos->children[$i]->children[$x]->children[$y]->icon = null;
                                            }
                                            $y++;
                                        }
                                        $rowGrupos->children[$i]->children[$x]->icon = null;
                                    }
                                    $x++;
                                }
                                $rowGrupos->children[$i]->icon = null;
                            }
                            $i++;
                        }
                        $rowGrupos->icon = null;
                    }
                    $main->children[] = $rowGrupos;
                }
            }
        }

        $padronCuenta[] = $main;

        return response()->json($padronCuenta, 200);
    }

    public function getAgregarItemEstructuraPlanCuenta(Request $request, PlanesCuentasRepository $repo)
    {
        if ($request->accion == 'ADD') {
            $repo->findByAgregarItemEstructuraPalnCuenta($request);
            return response()->json(["message" => "Registro agregado con éxito"]);
        } else {
            $repo->findByModificarItemEstructuraPalnCuenta($request, $request->id_detalle_plan);
            return response()->json(["message" => "Registro modificado con éxito"]);
        }
    }

    public function getEliminarDetalleItem(Request $request, PlanesCuentasRepository $repo)
    {
        if ($repo->findByExistMultiNivel($request->id_detalle_cuenta, $request->id_cuenta)) {
            return response()->json(["message" => "El registro que intenta eliminar tiene mutiples niveles asociados."], 409);
        }

        $repo->findByEliminarItem($request->id_detalle_cuenta, $request->id_cuenta);

        return response()->json(["message" => "Registro eliminado con éxito"]);
    }

    public function getListarCuentasPrincipales(Request $request, PlanesCuentasRepository $repo)
    {
        return response()->json($repo->findByDetalleCuentasPlanesPrincipal($request->id_nivel));
    }
    public function getListarCuentasCompleto(Request $request, PlanesCuentasRepository $repo)
    {
        $search = $request->query('search');
        return response()->json($repo->findByDetalleCuentasPlanesCompleto($search));
    }
}
