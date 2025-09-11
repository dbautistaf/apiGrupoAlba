<?php

namespace App\Http\Controllers\proveedor;

use App\Http\Controllers\prestadores\repository\PrestadorRepository;
use App\Http\Controllers\proveedor\Repository\ProveedorImputacionRepository;
use App\Http\Controllers\proveedor\Repository\ProveedorRepository;
use App\Models\proveedor\DatosBancariosEntity;
use App\Models\proveedor\MatrizProveedoresEntity;
use App\Models\proveedor\MetodoPagoProveedorEntity;
use App\Models\proveedor\ProveedorEntity;
use App\Models\proveedor\TipoProveedor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller
{

    public function getFiltrarProveedor(Request $request)
    {
        $data = [];

        if (!is_null($request->search)) {
            if (is_numeric($request->search)) {
                $data = ProveedorEntity::where("cuit", 'LIKE', '%' . $request->search . '%')->limit(5)->get();
            } else {
                $data = ProveedorEntity::where("razon_social", 'LIKE', '%' . $request->search . '%')->limit(5)->get();
            }
        } else {
            $data = ProveedorEntity::limit(10)
                ->get();
        }

        return response()->json($data, 200);
    }

    public function getProveedorId(Request $request)
    {
        return response()->json(ProveedorEntity::find($request->id), 200);
    }

    public function postProcesarProveedor(ProveedorRepository $repo, Request $request, ProveedorImputacionRepository $repoimputacion)
    {
        DB::beginTransaction();

        try {
            if (!empty($request->cod_proveedor)) {
                $datosBancarios = $request->datos_bancarios;
                $metodoPago = $request->metodo_pago;
                $repo->findByUpdateProveedor($request);
                if (is_numeric($datosBancarios['cod_dato_bancario'])) {
                    $repo->findByUpdateDatosBancarios($datosBancarios);
                } else {
                    $repo->findBySaveDatosBancarios($datosBancarios, $request->cod_proveedor);
                }

                if (is_numeric($metodoPago['id_pago_proveedor'])) {
                    $repo->findByUpdateMetodoPago($metodoPago, $request->cod_proveedor);
                } else {
                    $repo->findByMetodoPago($metodoPago, $request->cod_proveedor);
                }

                foreach ($request->detalle as $key) {
                    if (!is_null($key['id_imputacion_proveedor'])) {
                        $repoimputacion->findByUpdateImputaciones($key, $request->cod_proveedor);
                    } else {
                        $repoimputacion->findByAgregarImputaciones($key, $request->cod_proveedor);
                    }
                }
            } else {
                if ($repo->findByExisteCuit($request->cuit)) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "El N° de CUIT <b>" . $request->cuit . "</b>, ya se encuentra registrado en nuestro sistema."
                    ], 409);
                }

                $datosBancarios = $request->datos_bancarios;
                $metodoPago = $request->metodo_pago;
                $proveedor = $repo->findBySave($request);
                $repo->findBySaveDatosBancarios($datosBancarios, $proveedor->cod_proveedor);
                $repo->findByMetodoPago($metodoPago, $proveedor->cod_proveedor);

                foreach ($request->detalle as $key) {
                    if (!is_null($key['id_imputacion_proveedor'])) {
                        $repoimputacion->findByAgregarImputaciones($key, $proveedor->cod_proveedor);
                    }
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json(["message" => "Registro procesado correctamente"], 200);
    }

    public function getFiltrarMatrizProveedor(Request $request)
    {
        $data = [];
        if (!is_null($request->dev)) {
            if (!is_null($request->combo)) {
                $data = MatrizProveedoresEntity::with("usuario")->where(function ($query) use ($request) {
                    $query->where('cuit', 'LIKE', [$request->combo . '%'])
                        ->orWhere('razon_social', 'LIKE', [$request->combo . '%'])
                        ->orWhere('nombre_fantasia', 'LIKE', [$request->combo . '%']);
                })
                    ->orderBy('cod_proveedor')
                    ->get();
            } else if (!is_null($request->search)) {
                $data = MatrizProveedoresEntity::with("usuario")->where(function ($query) use ($request) {
                    $query->where('cuit', 'LIKE', [$request->search . '%'])
                        ->orWhere('razon_social', 'LIKE', [$request->search . '%'])
                        ->orWhere('nombre_fantasia', 'LIKE', [$request->search . '%']);
                })
                    ->orderBy('cod_proveedor')
                    ->get();
            } else {
                $data = MatrizProveedoresEntity::with("usuario")->orderBy('cod_proveedor')
                    ->get();
            }
        } else if (!is_null($request->search)) {
            if (is_numeric($request->search)) {
                $data = MatrizProveedoresEntity::with("usuario")->whereBetween('fecha_alta', [$request->desde, $request->hasta])
                    ->where('cuit', 'LIKE', ['%' . $request->search . '%'])
                    ->orderBy('cod_proveedor')
                    ->get();
            } else {
                $data = MatrizProveedoresEntity::with("usuario")->whereBetween('fecha_alta', [$request->desde, $request->hasta])
                    ->where('razon_social', 'LIKE', ['%' . $request->search . '%'])
                    ->orderBy('cod_proveedor')
                    ->get();
            }
        } else {
            $data = MatrizProveedoresEntity::with("usuario")->whereBetween('fecha_alta', [$request->desde, $request->hasta])
                ->orderBy('cod_proveedor')
                ->limit(100)
                ->get();
        }

        return response()->json($data, 200);
    }

    public function getProveedorMatrizId(Request $request)
    {
        return response()->json(MatrizProveedoresEntity::with(["tipoIva", "localidad", "datosBancarios", 'metodoPago'])
            ->find($request->id), 200);
    }

    public function getVencimientoPago(Request $request, PrestadorRepository $prestadorRepository)
    {
        $data = null;
        if (!is_null($request->cod_prestador)) {
            $data = $prestadorRepository->findByMetodoPago($request->cod_prestador);
        } else {
            $data = MetodoPagoProveedorEntity::where('cod_proveedor', $request->cod_proveedor)->first();
        }
        return response()->json($data, 200);
    }


    public function getEliminarProveedorMatrizId(Request $request)
    {
        $matriz = MatrizProveedoresEntity::find($request->id);
        $matriz->delete();

        return response()->json(["message" => "Registro eliminado correctamente"], 200);
    }

    public function postCreateFlash(ProveedorRepository $repo, Request $request)
    {
        return response()->json($repo->findBySaveFlash($request), 200);
    }

    public function getListarImputacionesProveedor(ProveedorImputacionRepository $repo, Request $request)
    {
        return response()->json($repo->findByListarImputaciones($request->codProveedor), 200);
    }

    public function getAnularImputacion(ProveedorImputacionRepository $repo, Request $request)
    {
        return response()->json($repo->findByAnularImputaciones($request), 200);
    }

    public function getListTipoProveedor()
    {
        return TipoProveedor::where('estado',1)->get();
    }

}
