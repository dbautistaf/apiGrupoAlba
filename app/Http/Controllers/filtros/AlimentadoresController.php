<?php

namespace App\Http\Controllers\filtros;

use App\Models\ComercialCajaModel;
use App\Models\ComercialOrigenModel;
use App\Models\EstadoPago;
use App\Models\EstadoTratamiento;
use App\Models\ModoEntrega;
use App\Models\prestadores\PrestadorEspecialidadesMedicasEntity;
use App\Models\prestadores\TipoMatriculaMedicosEntity;
use App\Models\TipoAlergiasEntity;
use App\Models\TipoBonoClinicoEntity;
use App\Models\TipoCoberturaRecetarioEntity;
use App\Models\prestadores\TipoCondicionIvaEntity;
use App\Models\prestadores\TipoImpuestosGananciasEntity;
use App\Models\prestadores\TipoPrestadorEntity;
use App\Models\TipoAutorizacion;
use App\Models\TipoTroquelEntity;
use App\Models\ubigeo\UbigeoLocalidadesEntity;
use App\Models\ubigeo\UbigeoProvinciasEntity;
use App\Models\vademecumModelo;
use App\Models\ZonaModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AlimentadoresController extends Controller
{
    public function getListarImpuestoGanancias()
    {
        $dtdatos = TipoImpuestosGananciasEntity::where('vigente', 1)
            ->orderBy('descripcion_tipo')
            ->get();
        return response()->json($dtdatos, 200);
    }

    public function getListarTipoCondicionIva()
    {
        $dtdatos = TipoCondicionIvaEntity::where('vigente', 1)
            ->orderBy('descripcion_iva')
            ->get();
        return response()->json($dtdatos, 200);
    }

    public function getListarTipoPrestador()
    {
        $dtdatos = TipoPrestadorEntity::where('vigente', 1)
            ->orderBy('descripcion')
            ->get();
        return response()->json($dtdatos, 200);
    }

    public function getListarProvincias()
    {
        $dtdatos = UbigeoProvinciasEntity::where('vigente', 1)
            ->orderBy('nombre_provincia')
            ->get();
        return response()->json($dtdatos, 200);
    }

    public function getListarProvinciasLocalidades($codProvincia)
    {
        $dtdatos = UbigeoLocalidadesEntity::with(["provincia"])
            ->where("cod_provincia", $codProvincia)
            ->where('vigente', 1)
            ->orderBy('nombre_localidad')
            ->get();
        return response()->json($dtdatos, 200);
    }

    public function getListarTipoMatriculas()
    {
        $dtdatos = TipoMatriculaMedicosEntity::where('vigente', 1)
            ->orderBy('descripcion_matricula')
            ->get();
        return response()->json($dtdatos, 200);
    }

    public function getListarEspecialidadesMedicas()
    {
        $dtdatos = PrestadorEspecialidadesMedicasEntity::where('vigente', 1)
            ->orderBy('descripcion_especialidad')
            ->get();
        return response()->json($dtdatos, 200);
    }

    public function getListarTipoBonosClinicos()
    {
        $dtdatos = TipoBonoClinicoEntity::where('vigente', 1)
            ->orderBy('descripcion')
            ->get();
        return response()->json($dtdatos, 200);
    }

    public function getListaTipoCobertura()
    {
        $dtdatos = TipoCoberturaRecetarioEntity::where('vigente', 1)
            ->orderBy('descripcion')
            ->get();
        return response()->json($dtdatos, 200);
    }

    public function getListaTipoTroquel()
    {
        $dtdatos = TipoTroquelEntity::where('vigente', 1)
            ->orderBy('cod_tipo_troquel')
            ->get();
        return response()->json($dtdatos, 200);
    }

    public function getListarMonodrogas(Request $request)
    {
        $data = [];

        if (!empty($request->searchs)) {
            $data =   vademecumModelo::where('nombre', 'like', '%' . $request->searchs . '%')
                ->orderByDesc('id_vademecum')
                ->limit(50)
                ->get();
        } else {
            $data =   vademecumModelo::orderByDesc('id_vademecum')
                ->limit(50)
                ->get();
        }

        return response()->json($data, 200);
    }

    public function getListaTipoAlergias()
    {
        $dtdatos = TipoAlergiasEntity::where('vigente', 1)
            ->orderBy('cod_tipo_alergia')
            ->get();
        return response()->json($dtdatos, 200);
    }

    public function getListaTipoAutorizacion()
    {
        $dtdatos = TipoAutorizacion::where('estado', 1)
            ->get();
        return response()->json($dtdatos, 200);
    }

    public function getListaModoEntrega()
    {
        $dtdatos = ModoEntrega::where('estado', 1)
            ->get();
        return response()->json($dtdatos, 200);
    }

    public function getListaEstadoTratamieno()
    {
        $dtdatos = EstadoTratamiento::where('estado', 1)
            ->get();
        return response()->json($dtdatos, 200);
    }

    public function getListaEstadoPago()
    {
        $dtdatos = EstadoPago::where('estado', 1)
            ->get();
        return response()->json($dtdatos, 200);
    }
    
    public function getListaZona()
    {
        $dtdatos = ZonaModelo::get();
        return response()->json($dtdatos, 200);
    }

    public function getListaComercialCaja(){
        return ComercialCajaModel::where('activo','1')->get();
    }

    public function getListaComercialOrigen($id){
        return ComercialOrigenModel::where('activo','1')->where('id_comercial_caja',$id)->get();
    }
}
