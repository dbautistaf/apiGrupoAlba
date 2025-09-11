<?php

namespace App\Http\Controllers\matrizPracticas;

use App\Exports\PracticasExport;
use App\Models\pratricaMatriz\PracticaMatrizEntity;
use App\Models\pratricaMatriz\PracticaNomencladorEntity;
use App\Models\pratricaMatriz\PracticaSeccionesEntity;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MantenimientoController extends Controller
{

    public function postAltaNomenclador(Request $request)
    {
        $estado = $request->vigente ? '1' : '0';
        $request->merge([
            'vigente' => $estado,
        ]);
        if (!empty($request->id_nomenclador)) {
            $nomenclador = PracticaNomencladorEntity::find($request->id_nomenclador);
            $nomenclador->update($request->all());
            return response()->json(["message" => "Registro actualizado correctamente"], 200);
        } else {
            PracticaNomencladorEntity::create($request->all());
            return response()->json(["message" => "Registro procesado correctamente"], 200);
        }
    }

    public function deleteNomenclador(Request $request)
    {
        $nomenclador = PracticaNomencladorEntity::find($request->id_nomenclador);
        $nomenclador->delete();
        return response()->json(["message" => "Registro eliminado correctamente"], 200);
    }

    public function getNomencladorId(Request $request)
    {
        $nomenclador = PracticaNomencladorEntity::find($request->id_nomenclador);

        return response()->json($nomenclador, 200);
    }

    public function postAltaSeccionNomenclador(Request $request)
    {
        $estado = $request->vigente ? '1' : '0';
        $request->merge([
            'vigente' => $estado,
        ]);
        if (!empty($request->id_seccion)) {
            $nomenclador = PracticaSeccionesEntity::find($request->id_seccion);
            $nomenclador->update($request->all());
            return response()->json(["message" => "Registro actualizado correctamente"], 200);
        } else {
            PracticaSeccionesEntity::create($request->all());
            return response()->json(["message" => "Registro procesado correctamente"], 200);
        }
    }

    public function deleteSeccionNomenclador(Request $request)
    {
        $nomenclador = PracticaSeccionesEntity::find($request->id_seccion);
        $nomenclador->delete();
        return response()->json(["message" => "Registro eliminado correctamente"], 200);
    }

    public function eliminarPractica(Request $request)
    {
        $nomenclador = PracticaMatrizEntity::find($request->codigo_practica);
        $nomenclador->delete();
        return response()->json(["message" => "Registro eliminado correctamente"], 200);
    }

    public function postAltaPractica(Request $request)
    {
        try {
            if (is_null($request->id_identificador_practica)) {
                PracticaMatrizEntity::create([
                    'codigo_practica' => $request->codigo_practica,
                    'id_seccion' => $request->id_seccion,
                    'nombre_practica' => $request->nombre_practica,
                    'cobertura' => $request->cobertura,
                    'coseguro' => $request->coseguro,
                    'vigente' => $request->vigente,
                    'norma' => $request->norma,
                    'id_padre' => $request->id_padre,
                    'cod_categoria_internacion' => $request->cod_categoria_internacion,
                    'id_practica_valorizacion' => $request->id_practica_valorizacion,
                    'id_tipo_galeno' => $request->id_tipo_galeno,
                    'especialista' => $request->especialista,
                    'ayudante_cantidad' => $request->ayudante_cantidad,
                    'ayudante' => $request->ayudante,
                    'anestesista' => $request->anestesista,
                    'galeno_gasto' => $request->galeno_gasto,
                    'valor_gasto' => $request->valor_gasto,
                    'galeno_adicional' => $request->galeno_adicional,
                    'valor_adicional' => $request->valor_adicional,
                    'galeno_aparatologia' => $request->galeno_aparatologia,
                    'valor_aparatologia' => $request->valor_aparatologia,
                    'fecha_vigencia' => $request->fecha_vigencia,
                    'id_nivel' => $request->id_nivel,
                    'id_tipo_valorizacion' => $request->id_tipo_valorizacion,
                    'representa_unidad' => $request->representa_unidad
                ]);
            } else {
                $practica = PracticaMatrizEntity::find($request->id_identificador_practica);
                $practica->codigo_practica = $request->codigo_practica;
                $practica->id_seccion = $request->id_seccion;
                $practica->nombre_practica = $request->nombre_practica;
                $practica->cobertura = $request->cobertura;
                $practica->coseguro = $request->coseguro;
                $practica->id_nivel = $request->id_nivel;
                $practica->id_tipo_valorizacion = $request->id_tipo_valorizacion;
                $practica->representa_unidad = $request->representa_unidad;
                $practica->update();
            }


            return response()->json(["message" => "Registro procesado correctamente"], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getBuscarPracticaId(Request $request)
    {
        $practica = PracticaMatrizEntity::with('seccion')
            ->find($request->id);

        return response()->json($practica, 200);
    }

    public function getExportarPractica()
    {
        return Excel::download(new PracticasExport(), 'practicas.xlsx');
    }


}
