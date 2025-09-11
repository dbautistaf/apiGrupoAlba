<?php

namespace App\Http\Controllers\mantenimiento;


use App\Models\DetalleRecetarioEntity;
use App\Models\RecetariosEntity;
use App\Models\RecetasModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPJasper\PHPJasper;

class RecetariosController extends Controller
{
    public function getConsultarRecetarios(Request $request)
    {
        $data = [];

        $data = RecetariosEntity::with(['afiliado', 'medico', 'estadoPrestacion', 'usuario', 'detalle', 'detalle.laboratorio'])
            ->whereBetween('fecha_registra', [$request->desde, $request->hasta])
            ->orderByDesc('cod_receta')
            ->limit(200)
            ->get();

        return response()->json($data, 200);
    }

    public function posCrearRecetario(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();

            $receta = RecetariosEntity::create([
                'fecha_registra' => $request->fecha_registra,
                'cod_profesional' => $request->cod_profesional,
                'dni_afiliado' => $request->dni_afiliado,
                'cod_tipo_estado' => $request->cod_tipo_estado,
                'usuario_registra' => $user->cod_usuario,
                'vigente' => $request->vigente
            ]);

            $detalle = $request->detalle;

            foreach ($detalle as $key) {
                DetalleRecetarioEntity::create([
                    'cod_receta' => $receta->cod_receta,
                    'cantidad_solicita' => $key["cantidad_solicita"],
                    'cod_laboratorio' => $key["cod_laboratorio"],
                    'diagnostico' => $key["diagnostico"]
                ]);
            }

            DB::commit();
            return response()->json(["message" => "Recetario registrado correctamente."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function putEditarRecetario(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();

            $receta = RecetariosEntity::find($request->cod_receta);
            $receta->fecha_registra = $request->fecha_registra;
            $receta->cod_profesional = $request->cod_profesional;
            $receta->dni_afiliado = $request->dni_afiliado;
            $receta->cod_tipo_estado = $request->cod_tipo_estado;
            $receta->usuario_registra = $user->cod_usuario;
            $receta->vigente = $request->vigente;

            $detalle = $request->detalle;

            DB::delete("DELETE FROM tb_detalle_recetario_medicacion WHERE cod_receta = ?", [$request->cod_receta]);

            foreach ($detalle as $key) {
                DetalleRecetarioEntity::create([
                    'cod_receta' => $receta->cod_receta,
                    'cantidad_solicita' => $key["cantidad_solicita"],
                    'cod_laboratorio' => $key["cod_laboratorio"],
                    'diagnostico' => $key["diagnostico"]
                ]);
            }

            DB::commit();
            return response()->json(["message" => "Recetario actualizado correctamente."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getBuscarRecetarioId($id)
    {
        $data = RecetariosEntity::with(['afiliado', 'medico', 'estadoPrestacion', 'usuario', 'detalle', 'detalle.laboratorio'])->find($id);
        return response()->json($data, 200);
    }

    public function getBuscarRecetarioDNI(Request $request)
    {
        $datos = $request->dni;
        $query = RecetasModelo::with('Afiliado', 'Farmacia')
            ->where(function ($query) use ($datos) {
                $query->whereHas('Afiliado', function ($queryAfiliado) use ($datos) {
                    $queryAfiliado->where('dni', $datos);
                });
            })->get();
        return response()->json($query, 200);
    }

    public function getImprimirReporte(Request $request)
    {
        $input = storage_path('app/public/reports/rpt_recetarios.jrxml');
        $output = storage_path('app/public/reports');
        $ruta = storage_path('app/public/');
        $options = [
            'format' => ['pdf'],
            'locale' => 'en',
            'params' => ['P_RUTA' => $ruta, 'P_ID' => $request->receta],
            'db_connection' => [
                'driver' => 'mysql',
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
                'host' => env('DB_HOST'),
                'database' => env('DB_DATABASE'),
                'port' => env('DB_PORT')
            ]
        ];

        $jasper = new PHPJasper;

        $jasper->process(
            $input,
            $output,
            $options
        )->execute();

        return response()->file(storage_path('app/public/reports/rpt_recetarios.pdf'), ['Content-Type' => 'application/pdf']);
    }
}
