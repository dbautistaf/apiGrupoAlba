<?php

namespace App\Http\Controllers\matrizPracticas;

use App\Http\Controllers\matrizPracticas\Repository\MatrizPracticasRepository;
use App\Models\pratricaMatriz\PracticaNomencladorEntity;
use App\Models\pratricaMatriz\PracticaPadreEntity;
use App\Models\pratricaMatriz\PracticaSeccionesEntity;
use App\Models\pratricaMatriz\PracticaTipoCoberturaEntity;
use App\Models\pratricaMatriz\PracticaTipoCoseguroEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class BuscardoresController extends Controller
{

    public function getFiltrarMatriz(MatrizPracticasRepository $repo, Request $request)
    {

        try {
            $data = [];

            if (
                empty($request->nomenclador)
                && empty($request->seccion)
                && empty($request->padre)
                && !empty($request->codigo)
                && empty($request->search)
                && empty($request->desde)
                && empty($request->hasta)
            ) {
                $data = $repo
                    ->findByListCodPracticaLike($request->codigo);
            } else if (
                empty($request->nomenclador)
                && empty($request->seccion)
                && empty($request->padre)
                && empty($request->codigo)
                && !empty($request->search)
                && empty($request->desde)
                && empty($request->hasta)
            ) {
                $data = $repo
                    ->findByListNombrePracticaLike($request->search);
            } else if (
                !empty($request->nomenclador)
                && empty($request->seccion)
                && empty($request->padre)
                && empty($request->codigo)
                && empty($request->search)
                && empty($request->desde)
                && empty($request->hasta)
            ) {
                $data = $repo
                    ->findByListIdNomenclador($request->nomenclador);
            } else if (
                !empty($request->nomenclador)
                && !empty($request->seccion)
                && empty($request->padre)
                && empty($request->codigo)
                && empty($request->search)
                && empty($request->desde)
                && empty($request->hasta)
            ) {
                $data = $repo
                    ->findByListIdSeccion($request->seccion);
            } else if (
                !empty($request->nomenclador)
                && !empty($request->seccion)
                && !empty($request->padre)
                && empty($request->codigo)
                && empty($request->search)
                && empty($request->desde)
                && empty($request->hasta)
            ) {
                $data = $repo
                    ->findByListIdNomencladorAndIdSeccionAndIdPadre($request->nomenclador, $request->seccion, $request->padre);
            } else if (
                empty($request->nomenclador)
                && empty($request->seccion)
                && !empty($request->padre)
                && empty($request->codigo)
                && empty($request->search)
                && empty($request->desde)
                && empty($request->hasta)
            ) {
                $data = $repo
                    ->findByListIdPadre($request->padre);
            } else if (
                !empty($request->nomenclador)
                && empty($request->seccion)
                && empty($request->padre)
                && empty($request->codigo)
                && empty($request->search)
                && !empty($request->desde)
                && !empty($request->hasta)
            ) {
                if (is_numeric($request->desde) && is_numeric($request->hasta)) {
                    $data = $repo
                        ->findByListCodPracticaBetweenAndNomenclador($request->desde, $request->hasta, $request->nomenclador);
                }
            } else if (
                !empty($request->nomenclador)
                && !empty($request->seccion)
                && empty($request->padre)
                && empty($request->codigo)
                && empty($request->search)
                && !empty($request->desde)
                && !empty($request->hasta)
            ) {
                if (is_numeric($request->desde) && is_numeric($request->hasta)) {
                    $data = $repo
                        ->findByListCodPracticaBetweenAndNomencladorAndSeccion($request->desde, $request->hasta, $request->nomenclador, $request->seccion);
                }
            } else if (
                !empty($request->nomenclador)
                && !empty($request->seccion)
                && !empty($request->padre)
                && empty($request->codigo)
                && empty($request->search)
                && !empty($request->desde)
                && !empty($request->hasta)
            ) {
                if (is_numeric($request->desde) && is_numeric($request->hasta)) {
                    $data = $repo
                        ->findByListCodPracticaBetweenAndNomencladorAndSeccionAndPadre($request->desde, $request->hasta, $request->nomenclador, $request->seccion, $request->padre);
                }
            } else if (
                !empty($request->nomenclador)
                && empty($request->seccion)
                && !empty($request->padre)
                && empty($request->codigo)
                && empty($request->search)
                && !empty($request->desde)
                && !empty($request->hasta)
            ) {
                if (is_numeric($request->desde) && is_numeric($request->hasta)) {
                    $data = $repo
                        ->findByListCodPracticaBetweenAndNomencladorAndPadre($request->desde, $request->hasta, $request->nomenclador, $request->padre);
                }
            } else if (
                empty($request->nomenclador)
                && empty($request->seccion)
                && empty($request->padre)
                && empty($request->codigo)
                && empty($request->search)
                && !empty($request->desde)
                && !empty($request->hasta)
            ) {
                if (is_numeric($request->desde) && is_numeric($request->hasta)) {
                    $data = $repo
                        ->findByListCodPracticaBetween($request->desde, $request->hasta);
                }
            } else if (
                !empty($request->nomenclador)
                && empty($request->seccion)
                && !empty($request->padre)
                && empty($request->codigo)
                && empty($request->search)
                && empty($request->desde)
                && empty($request->hasta)
            ) {
                $data = $repo
                    ->findByListNomencladorAndPadre($request->nomenclador, $request->padre);
            }

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getListarNomencladores()
    {
        $data = PracticaNomencladorEntity::where('vigente', '1')->get();

        return response()->json($data);
    }

    public function getListarTodoLossNomencladores(Request $request)
    {
        $data = PracticaNomencladorEntity::where('descripcion', 'like', '%' . $request->search . '%')->get();

        return response()->json($data);
    }

    public function getListarSecciones(Request $request)
    {
        $data = PracticaSeccionesEntity::where('id_nomenclador', $request->id)
            ->where('vigente', '1')
            ->get();

        return response()->json($data);
    }
    public function getListarTodaLasSecciones(Request $request)
    {
        $data = [];

        if (!is_null($request->search)) {
            $data = PracticaSeccionesEntity::where('id_nomenclador', $request->id)
                ->where('descripcion', 'like', '%' . $request->search . '%')
                ->orderBy('descripcion')
                ->get();
        } else {
            $data = PracticaSeccionesEntity::where('id_nomenclador', $request->id)
                ->orderBy('descripcion')
                ->get();
        }


        return response()->json($data);
    }
    public function getListarPracticasPadres()
    {
        $data = PracticaPadreEntity::where('vigente', '1')
            ->get();

        return response()->json($data);
    }
    public function getAdministrarBuscarPracticasMatriz(MatrizPracticasRepository $repo, Request $request)
    {
        $data = [];

        if (!empty($request->nomenclador) && empty($request->seccion) && empty($request->codigo) && empty($request->practica)) {
            $data = $repo->findByListIdNomenclador($request->nomenclador);
        } else if (!empty($request->nomenclador) && !empty($request->seccion) && empty($request->codigo) && empty($request->practica)) {
            $data = $repo->findByListIdNomencladorAndIdSeccion($request->nomenclador, $request->seccion);
        } else if (!empty($request->nomenclador) && !empty($request->seccion) && !empty($request->codigo) && empty($request->practica)) {
            $data = $repo->findByListIdNomencladorAndIdSeccionAndCodPracticaLike($request->nomenclador, $request->seccion, $request->codigo);
        } else if (!empty($request->nomenclador) && empty($request->seccion) && !empty($request->codigo) && empty($request->practica)) {
            $data = $repo->findByListCodPracticaLikeAndIdNomenclador($request->codigo, $request->nomenclador);
        } else if (empty($request->nomenclador) && empty($request->seccion) && empty($request->practica) && !empty($request->codigo) ) {
            $data = $repo->findByListCodPracticaLike($request->codigo);
        }else if (empty($request->nomenclador) && empty($request->seccion) && !empty($request->practica) && empty($request->codigo) ) {
            $data = $repo->findByListPracticaLike($request->practica);
        } else {
            $data = DB::select("SELECT * FROM vw_matriz_practicas  ORDER BY codigo_practica ASC LIMIT 50");
        }

        return response()->json($data);
    }

    public function getListarHistoricoPagosPracticaConvenio(Request $request)
    {
        $data = [];
        if (!is_null($request->search) && is_null($request->desde) && is_null($request->hasta)) {
            $data = DB::table('vw_convenio_practicas_historial_pago')
                ->where('cod_convenio', $request->convenio)
                ->where('codigo_practica', 'LIKE', $request->search . '%')
                ->whereBetween('fecha_update', [$request->desde, $request->hasta])
                ->orderByDesc('fecha_inicio')
                ->get();
        } else if (!is_null($request->desde) && !is_null($request->hasta) && is_null($request->search)) {
            $data = DB::table('vw_convenio_practicas_historial_pago')
                ->where('cod_convenio', $request->convenio)
                ->whereDate('fecha_inicio',   $request->desde)
                ->whereDate('fecha_fin', $request->hasta)
                ->orderByDesc('fecha_inicio')
                ->get();
        } else if (!is_null($request->desde) && !is_null($request->hasta) && !is_null($request->search)) {
            $data = DB::table('vw_convenio_practicas_historial_pago')
                ->where('codigo_practica', 'LIKE', $request->search . '%')
                ->where('cod_convenio', $request->convenio)
                ->whereDate('fecha_inicio',   $request->desde)
                ->whereDate('fecha_fin', $request->hasta)
                ->orderByDesc('fecha_inicio')
                ->get();
        } else {
            $data = DB::table('vw_convenio_practicas_historial_pago')
                ->where('cod_convenio', $request->convenio)
                ->whereBetween('fecha_update', [$request->desde, $request->hasta])
                ->orderByDesc('vigente')
                ->get();
        }
        return response()->json($data);
    }

    public function getListarCabeceraHistorial(Request $request)
    {
        $data = DB::select("SELECT fecha_inicio,fecha_fin FROM tb_convenios_historial_costos where cod_convenio = ?
        group by fecha_inicio,fecha_fin,cod_convenio order by fecha_inicio desc", [$request->convenio]);

        foreach ($data as $objeto) {
            $dash = DB::select("SELECT fecha_inicio,fecha_fin,valor_aumento_lineal,us.nombre_apellidos as usuario,h.tipo_aumento, h.observaciones,h.vigente
            FROM tb_convenios_historial_costos h  INNER JOIN tb_usuarios us ON  h.cod_usuario_crea = us.cod_usuario
            where cod_convenio = ? AND fecha_inicio = ? AND fecha_fin = ?
            ORDER BY h.observaciones DESC", [$request->convenio, $objeto->fecha_inicio, $objeto->fecha_fin]);
            if (count($dash) > 0) {
                $objeto->valor_aumento_lineal = $dash[0]->valor_aumento_lineal;
                $objeto->usuario = $dash[0]->usuario;
                $objeto->observaciones = $dash[0]->observaciones;
                $objeto->tipo_aumento = $dash[0]->tipo_aumento;
                $objeto->vigente = $dash[0]->vigente;

                $detallePracticas = DB::table('vw_convenio_practicas_historial_pago')
                    ->where('cod_convenio', $request->convenio)
                    ->whereDate('fecha_inicio',   $objeto->fecha_inicio)
                    ->whereDate('fecha_fin', $objeto->fecha_fin)
                    ->orderByDesc('fecha_inicio')
                    ->get();
                if (count($detallePracticas) > 0) {
                    $objeto->detalle = $detallePracticas;
                }
            }
            $objeto->show = false;
        }

        return response()->json($data);
    }

    public function getListarTipoCobertura()
    {
        $data = PracticaTipoCoberturaEntity::where('vigente', '1')
            ->orderBy('cobertura')
            ->get();
        return response()->json($data);
    }
    public function getListarTipoCoseguro()
    {
        $data = PracticaTipoCoseguroEntity::where('vigente', '1')
            ->get();

        return response()->json($data);
    }

    public function getComboMatriz(MatrizPracticasRepository $repo, Request $request)
    {
        $data = [];

        if (!is_null($request->search)) {
            $data = $repo->findByPracticaLikeLimit($request->search, $request->limit);
        } else {
            $data = $repo->findByLimit($request->limit);
        }

        return response()->json($data);
    }
}
