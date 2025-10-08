<?php

namespace App\Http\Controllers\mantenimiento;

use App\Models\HistoriaClinicaEntity;
use App\Models\HistorialClinicaFileModel;
use App\Models\RecetasModelo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HistoriaClinicaController extends Controller
{
    public function postCrearHistoriaClinica(Request $request)
    {
        DB::beginTransaction();
        $user = Auth::user();
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        $datos = json_decode($request->input('datos'));
        //return response()->json($datos, 500);
        $nombreArchivo = '';

        try {

            $newHistorial = HistoriaClinicaEntity::create([
                'dni_afiliado' => $datos->dni_afiliado->dni,
                'peso' => $datos->peso,
                'talla' => $datos->talla,
                'imc' => $datos->imc,
                'frecuencia_cardiaca' => $datos->frecuencia_cardiaca,
                'presion_arterial' => $datos->presion_arterial,
                'discapacida' => $datos->discapacida,
                'alergia' => $datos->alergia,
                'diagnostico' => $datos->diagnostico,
                'estudios_previos' => $datos->estudios_previos,
                'antecedentes' => $datos->antecedentes,
                'intolerancias_alimentarias' => $datos->intolerancias_alimentarias,
                'tratamiento_indicado' => $datos->tratamiento_indicado,
                'observaciones' => $datos->observaciones,
                'medicacion_solicitada' => $datos->medicacion_solicitada,
                'fecha_registra' => $fechaActual,
                'cod_usuario_registra' => $user->cod_usuario,
                'vigente' => $datos->vigente,
                'cod_tipo_alergia' => $datos->cod_tipo_alergia ? $datos->cod_tipo_alergia : 0,
                'id_tipo_discapacidad' => $datos->id_tipo_discapacidad ? $datos->id_tipo_discapacidad : 0,
                'cod_profesional' => $datos->cod_profesional,
                'cod_especialidad' => $datos->cod_especialidad,
                'url_file' => $nombreArchivo
            ]);

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $index => $file) {
                    $nombreArchivo = time() . $index . '.' . $file->extension();
                    $file->storeAs('historialclinico', $nombreArchivo, 'public');
                    HistorialClinicaFileModel::create([
                        'url_file' => $nombreArchivo,
                        'id_historia_clinica' => $newHistorial->id_historia_clinica,
                        'fecha_carga' =>  $fechaActual

                    ]);
                }
            }

            DB::commit();
            return response()->json(["message" => "La Historia clinica se registro correctamente."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function putUpdateHistoriaClinica(Request $request)
    {
        DB::beginTransaction();
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        $datos = json_decode($request->input('datos'));
        //return response()->json($datos, 500);
        $nombreArchivo = '';
        try {

            $historia = HistoriaClinicaEntity::find($datos->id_historia_clinica);

            $historia->dni_afiliado = $datos->dni_afiliado;
            $historia->peso = $datos->peso;
            $historia->talla = $datos->talla;
            $historia->imc = $datos->imc;
            $historia->frecuencia_cardiaca = $datos->frecuencia_cardiaca;
            $historia->presion_arterial = $datos->presion_arterial;
            $historia->discapacida = $datos->discapacida;
            $historia->alergia = $datos->alergia ?? 0;
            $historia->diagnostico = $datos->diagnostico;
            $historia->estudios_previos = $datos->estudios_previos;
            $historia->antecedentes = $datos->antecedentes;
            $historia->intolerancias_alimentarias = $datos->intolerancias_alimentarias;
            $historia->tratamiento_indicado = $datos->tratamiento_indicado;
            $historia->observaciones = $datos->observaciones;
            $historia->medicacion_solicitada = $datos->medicacion_solicitada;
            $historia->vigente = $datos->vigente;
            $historia->cod_tipo_alergia = (!isset($datos->cod_tipo_alergia) || $datos->cod_tipo_alergia === '')
                ? 0
                : $datos->cod_tipo_alergia;
            $historia->id_tipo_discapacidad =  (!isset($datos->id_tipo_discapacidad) || $datos->id_tipo_discapacidad === '')
                ? 0
                : $datos->id_tipo_discapacidad;
            $historia->cod_profesional = $datos->cod_profesional;
            $historia->cod_especialidad = $datos->cod_especialidad;
            $historia->save();

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $index => $file) {
                    $nombreArchivo = time() . $index . '.' . $file->extension();
                    $file->storeAs('historialclinico', $nombreArchivo, 'public');
                    HistorialClinicaFileModel::create([
                        'url_file' => $nombreArchivo,
                        'id_historia_clinica' => $historia->id_historia_clinica,
                        'fecha_carga' =>  $fechaActual

                    ]);
                }
            }

            DB::commit();
            return response()->json(["message" => "La Historia clinica se actualizo correctamente."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function deleteHistoriaClinica($id)
    {
        $historia = HistoriaClinicaEntity::find($id);
        $historia->delete();
        return response()->json(["message" => "La Historia clinica se elimino correctamente."], 200);
    }

    public function getHistoriaClinicaId($id)
    {
        $historia = HistoriaClinicaEntity::find($id);

        return response()->json($historia, 200);
    }

    public function getListarHistoriaClinica(Request $request)
    {

        if (!empty($request->search)) {
            if (is_numeric($request->search)) {
                $data =  HistoriaClinicaEntity::with(['afiliado', 'tipoAlergia', 'tipoDiscapacidad', 'profesional', 'especialidad','filehistoriaclinica'])
                    ->where('dni_afiliado', 'like', '%' . $request->search . '%')
                    ->whereBetween('fecha_registra', [$request->desde, $request->hasta])
                    ->get();
            } else {
                $data =  HistoriaClinicaEntity::with(['afiliado', 'tipoAlergia', 'tipoDiscapacidad', 'profesional', 'especialidad','filehistoriaclinica'])
                    ->whereHas('afiliado', function ($query) use ($request) {
                        $query->where('apellidos', 'like', '%' . $request->search . '%');
                    })
                    ->whereBetween('fecha_registra', [$request->desde, $request->hasta])
                    ->get();
            }
        } else {
            $data =  HistoriaClinicaEntity::with(['afiliado', 'tipoAlergia', 'tipoDiscapacidad', 'profesional', 'especialidad','filehistoriaclinica'])
                ->whereBetween('fecha_registra', [$request->desde, $request->hasta])
                ->get();
        }

        return response()->json($data, 200);
    }

    public function getBuscarHistoriaClinicaAfiliadoDNI(Request $request)
    {

        $dni = $request->dni;

        $query = RecetasModelo::with(['Afiliado', 'Farmacia', 'detalleReceta.vademecum'])
            ->whereHas('Afiliado', function ($q) use ($dni) {
                $q->where('dni', $dni);
            });

        $data = HistoriaClinicaEntity::with(['afiliado', 'tipoAlergia', 'tipoDiscapacidad', 'profesional', 'especialidad','filehistoriaclinica'])
            ->where('dni_afiliado', $request->dni)
            ->orderByDesc('id_historia_clinica')
            ->get();

        return response()->json($data, 200);
    }
}
