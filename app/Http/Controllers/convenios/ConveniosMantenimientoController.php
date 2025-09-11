<?php

namespace App\Http\Controllers\convenios;

use App\Http\Controllers\convenios\Repository\ConvenioRepository;
use App\Models\convenios\ConveniosDatosBancariosPrestadorEntity;
use App\Models\convenios\ConveniosEntity;
use App\Models\convenios\ConveniosPrestadoresEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ConveniosMantenimientoController extends Controller
{
    public function postCrearConvenio(ConvenioRepository $repoConvenios, Request $request)
    {
        DB::beginTransaction();
        try {

            $convenio = $repoConvenios->findBysave($request);

            /* @LOCALIDADES */
            $localidadesOptions = $request->localidades;
            foreach ($localidadesOptions as $value) {
                if (!empty($value['cod_localidad'])) {
                    $convenio->localidades()->attach($value['cod_localidad']);
                }
            }

            /* @CATEGORIA PPAGO */
            $categoriaPagos = $request->categoriaPagos;
            foreach ($categoriaPagos as $value) {
                if (!empty($value['estado']) && $value['estado'] == true) {
                    $convenio->categoriaPagos()->attach($value['id_categoria_pago']);
                }
            }

            /* @TIPO DE VALORIZACION */
            $tipoValorizacion = $request->tipoValorizacion;
            foreach ($tipoValorizacion as $value) {
                if (!empty($value['estado']) && $value['estado'] == true) {
                    $convenio->tipoValorizacion()->attach($value['id_tipo_valotizacion']);
                }
            }

            /* @TIPO DE ALTA CATEGORIAS */
            $altasCategorias = $request->altasCategorias;
            foreach ($altasCategorias as $value) {
                if (!empty($value['id_alta_categoria'])) {
                    $convenio->altasCategorias()->attach($value['id_alta_categoria']);
                }
            }

            /* @TIPO PLANES DEL CONVENIO */
            $planes = $request->planes;
            foreach ($planes as $value) {
                if (!empty($value['id_conf_galeno_plan'])) {
                    $convenio->tipoPlanes()->attach($value['id_conf_galeno_plan']);
                }
            }
            /* @TIPO COBERTURAS */
            $tipoCoberturasOptions = $request->tipo_coberturas;
            foreach ($tipoCoberturasOptions as $value) {
                if (!empty($value['id_tipo'])) {
                    $convenio->tipoCoberturas()->attach($value['id_tipo']);
                }
            }

            /* @TIPO LOCATARIOS */
            $locatariosOptions = $request->detalle_locatorios;
            foreach ($locatariosOptions as $value) {
                if (!empty($value['id_locatorio'])) {
                    $convenio->locatarios()->attach($value['id_locatorio']);
                }
            }

            /* @ORIGEN */
            $origenOptions = $request->detalle_origen;
            foreach ($origenOptions as $value) {
                if (!empty($value['id_tipo_origen'])) {
                    $convenio->origen()->attach($value['id_tipo_origen']);
                }
            }

            DB::commit();
            return response()->json(["message" => "Contrato creado correctamente"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getObtenerConevenioId($id)
    {
        return response()->json(ConveniosEntity::with(['provincia', 'localidades', 'categoriaPagos', 'tipoValorizacion', 'altasCategorias', 'tipoCoberturas', 'tipoPlanes', 'locatarios','origen'])
            ->find($id));
    }

    public function getObtenerConevenioIdPrincipal(ConvenioRepository $repo, $id)
    {
        return response()->json($repo->findByIdPricipal($id));
    }

    public function postUpdateConvenio(ConvenioRepository $repoConvenios, Request $request)
    {
        DB::beginTransaction();
        try {
            $convenio = $repoConvenios->findBysaveId($request);

            /* @CATEGORIA PPAGO */
            $categoriaPagos = $request->categoriaPagos;
            $addCat = array();
            foreach ($categoriaPagos as $value) {
                if (!empty($value['estado']) && $value['estado'] == true) {
                    $addCat[] = array($value['id_categoria_pago']);
                }
            }
            $filterCat = call_user_func_array('array_merge', $addCat);
            if (count($filterCat) > 0) {
                $convenio->categoriaPagos()->sync($filterCat);
            }

            /* @TIPO DE VALORIZACION */
            $tipoValorizacion = $request->tipoValorizacion;
            $addtipValor = array();
            foreach ($tipoValorizacion as $value) {
                if (!empty($value['estado']) && $value['estado'] == true) {
                    $addtipValor[] = array($value['id_tipo_valotizacion']);
                }
            }
            $filterTipovalor = call_user_func_array('array_merge', $addtipValor);
            if (count($filterTipovalor) > 0) {
                $convenio->tipoValorizacion()->sync($filterTipovalor);
            }

            /* @TIPO DE ALTA CATEGORIAS */
            $altasCategorias = $request->altasCategorias;
            $addAltasCat = array();
            foreach ($altasCategorias as $value) {
                if (!empty($value['id_alta_categoria'])) {
                    $addAltasCat[] = array($value['id_alta_categoria']);
                }
            }
            $filterAltasCat = call_user_func_array('array_merge', $addAltasCat);
            if (count($filterAltasCat) > 0) {
                $convenio->altasCategorias()->sync($filterAltasCat);
            }

            /* @TIPO PLANES DEL CONVENIO */
            $planes = $request->planes;
            $addPlanes = array();
            foreach ($planes as $value) {
                if (!empty($value['id_conf_galeno_plan'])) {
                    $addPlanes[] = array($value['id_conf_galeno_plan']);
                }
            }
            $filterPlanes = call_user_func_array('array_merge', $addPlanes);
            if (count($filterPlanes) > 0) {
                $convenio->tipoPlanes()->sync($filterPlanes);
            }
            /* @LOCALIDADES */
            $localidadesOptions = $request->localidades;
            $addLocalidades = array();
            foreach ($localidadesOptions as $value) {
                if (!empty($value['cod_localidad'])) {
                    $addLocalidades[] = array($value['cod_localidad']);
                }
            }
            $filterLocalidades = call_user_func_array('array_merge', $addLocalidades);
            if (count($filterLocalidades) > 0) {
                $convenio->localidades()->sync($filterLocalidades);
            }
            /* @TIPO COBERTURAS */
            $tipoCoberturasOptions = $request->tipo_coberturas;
            $addTipoCoberturas = array();
            foreach ($tipoCoberturasOptions as $value) {
                if (!empty($value['id_tipo'])) {
                    $addTipoCoberturas[] = array($value['id_tipo']);
                }
            }
            $filterTipoCoberturas = call_user_func_array('array_merge', $addTipoCoberturas);
            if (count($filterTipoCoberturas) > 0) {
                $convenio->tipoCoberturas()->sync($filterTipoCoberturas);
            }

            /* @TIPO LOCATARIOS */
            $locatariosOptions = $request->detalle_locatorios;
            $addLocatarios = array();
            foreach ($locatariosOptions as $value) {
                if (!empty($value['id_locatorio'])) {
                    $addLocatarios[] = array($value['id_locatorio']);
                }
            }
            $filterLocatarios = call_user_func_array('array_merge', $addLocatarios);
            if (count($filterLocatarios) > 0) {
                $convenio->locatarios()->sync($filterLocatarios);
            }

            /* @ORIGEN */
            $origenOptions = $request->detalle_origen;
            $addOrigen = array();
            foreach ($origenOptions as $value) {
                if (!empty($value['id_tipo_origen'])) {
                    $addOrigen[] = array($value['id_tipo_origen']);
                }
            }
            $filterOrigen = call_user_func_array('array_merge', $addOrigen);
            if (count($filterOrigen) > 0) {
                $convenio->origen()->sync($filterOrigen);
            }

            DB::commit();
            return response()->json(["message" => "Convenio actualizado correctamente"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function deleteConvenioId($id)
    {
        DB::beginTransaction();
        try {
            DB::delete('DELETE FROM tb_convenios_categorias WHERE cod_convenio = ?', [$id]);
            DB::delete('DELETE FROM tb_convenios_datos_bancarios_prestador WHERE cod_convenio_prestador IN (SELECT cod_convenio_prestador FROM tb_convenios_prestador WHERE cod_convenio = ?)', [$id]);
            DB::delete('DELETE FROM tb_convenios_prestador WHERE cod_convenio = ?', [$id]);
            DB::delete('DELETE FROM tb_convenios_detalle_modulo WHERE id_modulo IN (SELECT id_modulo FROM tb_convenios_modulos WHERE id_convenio = ?)', [$id]);
            DB::delete('DELETE FROM tb_convenios_modulos WHERE id_convenio = ?', [$id]);
            DB::delete('DELETE FROM tb_convenios_detalle_negociacion WHERE id_negociacion IN  (SELECT id_negociacion FROM tb_convenios_negociacion_contratos WHERE cod_convenio = ?)', [$id]);
            DB::delete('DELETE FROM tb_convenios_negociacion_respuestas WHERE id_negociacion IN (SELECT id_negociacion FROM tb_convenios_negociacion_contratos WHERE cod_convenio = ?)', [$id]);
            DB::delete('DELETE FROM tb_convenios_documentacion WHERE cod_convenio = ? ', [$id]);
            DB::delete('DELETE FROM tb_convenios_exclusion_inclusion WHERE id_convenio = ? ', [$id]);
            DB::delete('DELETE FROM tb_convenios_gelenos WHERE cod_convenio = ? ', [$id]);
            DB::delete('DELETE FROM tb_convenios_normas_operativas WHERE cod_convenio = ? ', [$id]);
            DB::delete('DELETE FROM tb_convenios_pagos WHERE cod_convenio = ? ', [$id]);
            DB::delete('DELETE FROM tb_convenios_planes WHERE cod_convenio = ? ', [$id]);
            DB::delete('DELETE FROM tb_convenios_practicas WHERE cod_convenio = ? ', [$id]);
            DB::delete('DELETE FROM tb_convenios_valorizacion WHERE cod_convenio = ? ', [$id]);
            DB::delete('DELETE FROM tb_convenios_historial_costos WHERE cod_convenio = ? ', [$id]);

            $convenio = ConveniosEntity::find($id);
            $convenio->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json(["message" => "Convenio eliminado correctamente"]);
    }

    public function postAgregarPrestadorConvenio(Request $request)
    {
        try {
            $data = ConveniosPrestadoresEntity::create([
                'cod_prestador' => $request->cod_prestador,
                'estado' => $request->estado,
                'cod_convenio' => $request->cod_convenio,
                'id_tipo_comprobantes' => $request->id_tipo_comprobantes,
                'iva_id_alicuota_iva' => $request->iva_id_alicuota_iva,
                'id_tipo_valor_pago' => $request->id_tipo_valor_pago,
                'forma_pago' => $request->forma_pago
            ]);

            if (!is_null($request->datos_bancarios['cbu'])) {
                ConveniosDatosBancariosPrestadorEntity::create([
                    'cbu' => $request->datos_bancarios['cbu'],
                    'descripcion' => $request->datos_bancarios['cbu'],
                    'cod_convenio_prestador' => $data->cod_convenio_prestador,
                    'id_tipo_cbu' => $request->datos_bancarios['id_tipo_cbu'],
                    'vigente' => '1'
                ]);
            }
            return response()->json(["message" => "Prestador registrado correctamente"]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function postUpdatePrestadorConvenio(Request $request)
    {
        try {
            $prestador = ConveniosPrestadoresEntity::find($request->cod_convenio_prestador);
            $prestador->cod_prestador = $request->cod_prestador;
            $prestador->estado = $request->estado;
            $prestador->cod_convenio = $request->cod_convenio;
            $prestador->id_tipo_comprobantes = $request->id_tipo_comprobantes;
            $prestador->iva_id_alicuota_iva = $request->iva_id_alicuota_iva;
            $prestador->id_tipo_valor_pago = $request->id_tipo_valor_pago;
            $prestador->forma_pago = $request->forma_pago;
            $prestador->update();

            $bancario = ConveniosDatosBancariosPrestadorEntity::find($request->datos_bancarios['id_datos_bancarios_prestador']);
            if ($bancario == null) {
                ConveniosDatosBancariosPrestadorEntity::create([
                    'cbu' => $request->datos_bancarios['cbu'],
                    'descripcion' => 'SIN NOVEDAD',
                    'cod_convenio_prestador' => $request->cod_convenio_prestador,
                    'id_tipo_cbu' => $request->datos_bancarios['id_tipo_cbu'],
                    'vigente' => 1
                ]);
            } else {
                $bancario->cbu = $request->datos_bancarios['cbu'];
                $bancario->id_tipo_cbu = $request->datos_bancarios['id_tipo_cbu'];
                $bancario->update();
            }


            return response()->json(["message" => "Prestador actualizado correctamente"]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function deleteConvenioPrestadorId($id)
    {
        $convenio = ConveniosPrestadoresEntity::find($id);
        $datosBancarios = ConveniosDatosBancariosPrestadorEntity::where('cod_convenio_prestador', $id)->first();
        if (!is_null($datosBancarios)) {
            $datosBancarios->delete();
        }

        $convenio->delete();

        return response()->json(["message" => "El Prestador fue eliminado del convenio correctamente"]);
    }
}
