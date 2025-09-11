<?php

namespace App\Http\Controllers;

use App\Models\afiliado\AfiliadoPadronEntity;
use App\Models\MotivosBajaModel;
use App\Models\PracticasDiscaPacidadModel;
use App\Models\prestadores\PrestadorEntity;
use App\Models\ProvinciaDiscapacidadModel;
use App\Models\TipoComprobanteModel;
use App\Models\TipoEmisionModel;
use App\Models\tipoEntidadModels;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FiltersController extends AuthController
{
    public function srvFilterPadron(Request $request)
    {
        $data = [];
        try {
            if (strlen($request->dni) === 8) {
                $data = AfiliadoPadronEntity::with(['certificado', 'detalleplan.TipoPlan', 'tipoParentesco'])->where('dni', $request->dni)->get();
            } else if (strlen($request->dni) === 11) {
                $data = AfiliadoPadronEntity::with(['certificado', 'detalleplan.TipoPlan', 'tipoParentesco'])->where('cuil_benef', $request->dni)->get();
            } else if (empty($request->dni)) {
                $data = AfiliadoPadronEntity::with(['certificado', 'detalleplan.TipoPlan', 'tipoParentesco'])->orderBy('id')->limit(7)->get();
            } else {
                $data = AfiliadoPadronEntity::with(['certificado', 'detalleplan.TipoPlan', 'tipoParentesco'])
                    ->where('dni', 'like', '%' . $request->dni . '%')
                    ->orWhere('cuil_benef', 'like', '%' . $request->dni . '%')
                    ->orderBy('id')
                    ->limit(7)
                    ->get();
            }

            if (count($data) == 0) {
                return response()->json(["message" => "No se encontro resultados para <b>" . $request->dni . "</b>"], 404);
            }

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function srvFilterTipoComprobantes()
    {
        $data = [];

        $data = TipoComprobanteModel::where('vigente', 1)->get();

        return response()->json($data, 200);
    }

    public function srvProvinciasDiscapacidad()
    {
        $data = [];

        $data = ProvinciaDiscapacidadModel::get();

        return response()->json($data, 200);
    }

    public function srvFilterTipoEmisionComprobantes()
    {
        $data = [];

        $data = TipoEmisionModel::where('vigente', 1)->get();

        return response()->json($data, 200);
    }

    public function srvFiltersPracticasDiscapacidad(Request $request)
    {
        $data = [];

        if (strlen($request->search) === 3) {
            $data = PracticasDiscaPacidadModel::where('id_practica', $request->search)->get();
        } else {
            $data = PracticasDiscaPacidadModel::get();
        }

        return response()->json($data, 200);
    }

    public static function srvBuscarProvedor(Request $request)
    {

        $data = DB::select('select * from tb_provedores_discapacidad where cuit = ?', [$request->cuit]);

        return response()->json($data, 200);
    }

    public function srvListaDocumentacionpresupuesto()
    {
        $data = DB::select('select * from tb_tipo_documentacion_presupuesto');

        return response()->json($data, 200);
    }

    public function srvListaTipoEntidad()
    {
        $data = tipoEntidadModels::get();

        return response()->json($data, 200);
    }
    
    public function srvFilterPadronUser()
    {

        $user = Auth::user();
        try {
            $data = AfiliadoPadronEntity::with(['certificado', 'detalleplan.TipoPlan', 'tipoParentesco'])->where('dni', $user->documento)->first();
            $fechaNacimiento = Carbon::parse($data->fe_nac);
            $fechaActual = Carbon::now();
            $diferencia = $fechaNacimiento->diff($fechaActual);
            $data->edad = $diferencia->y;
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            throw $th;
        }
       
    }

    public function srvListaMotivoBaja()
    {
        $data = MotivosBajaModel::get();
        return response()->json($data, 200);
    }
}
