<?php

namespace App\Http\Controllers\Coseguros\Services;

use App\Http\Controllers\Coseguros\Repository\MatrizCoseguroRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class CosegurosController extends Controller
{

    protected $repoMatriz;

    public function __construct(MatrizCoseguroRepository $repoMatriz)
    {
        $this->repoMatriz = $repoMatriz;
    }

    public function consultarCoseguros()
    {
        return response()->json($this->repoMatriz->findByListarCoseguros());
    }

    public function actualizarCostos(Request $request)
    {
        try {
            DB::beginTransaction();
            $detalle = json_decode($request->detalle);
            foreach ($detalle as $key) {
                $this->repoMatriz->findByUpdate($key);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Matriz actualizada con éxito']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al actualizar coseguros', 'error' => $th->getMessage()], 500);
        }
    }
}
