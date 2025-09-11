<?php

namespace App\Http\Controllers\mantenimiento;

use App\Models\BonoClinicoEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPJasper\PHPJasper;

class MantenimientoBonoClinicoController extends Controller
{

    public function postCrearBonoClinico(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();

            BonoClinicoEntity::create([
                'dni_afiliado' => $request->dni_afiliado,
                'fecha_registra' => $request->fecha_registra,
                'costo_bono' => $request->costo_bono,
                'diagnostico' => $request->diagnostico,
                'especialidad'=> $request->especialidad,
                'observacion' => $request->observacion,
                'vigente' => $request->vigente,
                'cod_tipo_bono' => $request->cod_tipo_bono,
                'cod_profesional' => $request->cod_profesional,
                'cod_usuario_registra'  => $user->cod_usuario
            ]);

            DB::commit();
            return response()->json(["message" => "Bono registrado correctamente."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function putActualizarBonoClinico(Request $request)
    {
        try {
            DB::beginTransaction();
            $bonoClinico = BonoClinicoEntity::find($request->cod_bono);

            $bonoClinico->update($request->all());

            DB::commit();
            return response()->json(["message" => "Bono actualizado correctamente."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getConsultarBonos(Request $request)
    {
        $data = [];

        if (!empty($request->searchs)) {
            if (is_numeric($request->searchs)) {
                $data = BonoClinicoEntity::with(['afiliado', 'medico', 'tipoBono'])
                    ->whereHas('afiliado', function ($query) use ($request) {
                        $query->where('dni', 'like', '%' . $request->searchs . '%');
                        $query->orWhere('cuil_benef', 'like', '%' . $request->searchs . '%');
                    })
                    ->whereBetween('fecha_registra', [$request->desde, $request->hasta])
                    ->get();
            } else {
                $data = BonoClinicoEntity::with(['afiliado', 'medico', 'tipoBono','usuario'])
                    ->whereHas('afiliado', function ($query) use ($request) {
                        $query->where('nombre', 'like', '%' . $request->searchs . '%');
                        $query->orWhere('apellidos', 'like', '%' . $request->searchs . '%');
                    })
                    ->whereBetween('fecha_registra', [$request->desde, $request->hasta])
                    ->get();
            }
        } else {
            $data = BonoClinicoEntity::with(['afiliado', 'medico', 'tipoBono','usuario'])
                ->whereBetween('fecha_registra', [$request->desde, $request->hasta])
                ->orderByDesc('cod_bono')
                ->limit(200)
                ->get();
        }

        return response()->json($data, 200);
    }

    public function deleteEliminarBonoClinico($id)
    {
        BonoClinicoEntity::destroy($id);
        return response()->json(["message" => "Bono eliminado correctamente."], 200);
    }

    public function getBuscarBonoID($id)
    {

        return response()->json(BonoClinicoEntity::with(['afiliado', 'medico', 'tipoBono','usuario'])->find($id), 200);
    }

    public function getBuscarBonosAfiliadoDNI(Request $request)
    {
        $data = BonoClinicoEntity::with(['afiliado', 'medico', 'tipoBono','usuario'])
        ->where('dni_afiliado', $request->dni)
        ->orderByDesc('cod_bono')
        ->get();

        return response()->json($data, 200);
    }

    public function getImprimirReporte(Request $request)
    {
        $input = storage_path('app/public/reports/rpt_bonos.jrxml');
        $output = storage_path('app/public/reports');
        $ruta = storage_path('app/public/');
        $options = [
            'format' => ['pdf'],
            'locale' => 'en',
            'params' => ['P_RUTA' => $ruta, 'P_ID' => $request->bono],
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

        return response()->file(storage_path('app/public/reports/rpt_bonos.pdf'), ['Content-Type' => 'application/pdf']);
    }
    
    public function getConsultarBonosUser(Request $request)
    {
        $data = [];
        $user = Auth::user();
        if (!empty($request->searchs)) {
            if (is_numeric($request->searchs)) {
                $data = BonoClinicoEntity::with(['afiliado', 'medico', 'tipoBono'])
                    ->whereHas('afiliado', function ($query) use ($user) {
                        $query->where('dni', $user->documento);
                    })
                    ->whereBetween('fecha_registra', [$request->desde, $request->hasta])
                    ->get();
            }
        } else {
                $data = BonoClinicoEntity::with(['afiliado', 'medico', 'tipoBono'])
                    ->whereHas('afiliado', function ($query) use ($user) {
                        $query->where('dni', $user->documento);
                    })
                ->orderByDesc('cod_bono')
                ->limit(200)
                ->get();
        }

        return response()->json($data, 200);
    }
}
