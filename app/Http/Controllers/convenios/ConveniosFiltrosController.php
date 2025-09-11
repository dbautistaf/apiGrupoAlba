<?php

namespace App\Http\Controllers\convenios;

use App\Http\Controllers\convenios\Repository\ConvenioRepository;
use App\Http\Controllers\convenios\Repository\PrestadoresConvenioRepository;
use App\Models\convenios\ConveniosTipoUnidadesEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ConveniosFiltrosController extends Controller
{
    public function getListarConvenios(ConvenioRepository $repo, Request $request)
    {
        return response()->json($repo->findByListFiltrarConvenios($request));
    }

    public function getListarPrestadorConvenio(PrestadoresConvenioRepository $repo, Request $request)
    {
        return response()->json($repo->findByListPrestadores($request->cod_convenio));
    }

    public function getListarTipoUnidadConvenio()
    {
        try {
            $data = ConveniosTipoUnidadesEntity::orderBy('descripcion')
                ->get();

            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getListarPracticasConvenio(Request $request)
    {
        try {
            $data = [];

            if ($request->accion === 'GRUPO' && is_null($request->search)) {
                $data = DB::select("SELECT * FROM vw_convenio_practicas
                 WHERE cod_convenio = ? AND fecha_vigencia  = ? ", [$request->convenio, $request->grupo]);
            } else if ($request->accion === 'GRUPO' && !is_null($request->search)) {
                $data = DB::table('vw_convenio_practicas')
                    ->where('vigente', '1')
                    ->where('cod_convenio', $request->convenio)
                    ->where('fecha_vigencia', $request->grupo)
                    ->where(function ($query) use ($request) {
                        $query->where('codigo_practica', 'LIKE', $request->search . '%')
                            ->orWhere('nombre_practica', 'LIKE', '%' . $request->search . '%');
                    })
                    ->get();
            } else if ($request->accion === 'VIGENTE' && !is_null($request->search)) {
                $data = DB::table('vw_convenio_practicas')
                    ->where('vigente', '1')
                    ->where('cod_convenio', $request->convenio)
                    ->where(function ($query) use ($request) {
                        $query->where('codigo_practica', 'LIKE', $request->search . '%')
                            ->orWhere('nombre_practica', 'LIKE', '%' .$request->search . '%');
                    })
                    ->get();
            } else {
                $data = DB::select("SELECT * FROM vw_convenio_practicas
                WHERE cod_convenio = ? AND vigente = ? ", [$request->convenio, $request->vigente]);

                if (count($data) == 0) {
                    $data = DB::select("SELECT * FROM vw_convenio_practicas
    WHERE cod_convenio = ? ", [$request->convenio]);
                }
            }



            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
