<?php

namespace App\Http\Controllers\afiliados\Services;

use App\Http\Controllers\afiliados\repository\ProgramaEspecialRepository;
use App\Http\Controllers\Utils\ManejadorDeArchivosUtils;
use App\Models\afiliado\AfiliadoPadronEntity;
use App\Models\afiliado\ProgramaEspecialAfiEntity;
use App\Models\afiliado\TipoProgramaEspecialAfiEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProgramaEspecialController extends Controller
{

    protected $repoPrograma;
    protected $repoArchivo;
    public function __construct(ProgramaEspecialRepository $repoPrograma, ManejadorDeArchivosUtils $repoArchivo)
    {
        $this->repoPrograma = $repoPrograma;
        $this->repoArchivo = $repoArchivo;
    }

    public function tipoPrograma()
    {
        return response()->json($this->repoPrograma->findByListarTipos());
    }

    public function procesarPrograma(Request $request)
    {
        try {
            DB::beginTransaction();

            $programa = json_decode($request->programa);
            if (is_numeric($programa->id_programa)) {
                $fileDocumentacion = $this->repoArchivo->findBySubirDocumento('TRAN-INCUCAI' . $programa->dni_afiliado, 'afiliados/programa', $request->file('documentacion'));
                $this->repoPrograma->findByUpdate($programa, $fileDocumentacion);

                DB::commit();
                return response()->json(['success' => true, 'message' => 'Programa especial actualizado con éxito']);
            } else {
                $fileDocumentacion = $this->repoArchivo->findBySubirDocumento('TRAN-INCUCAI' . $programa->dni_afiliado, 'afiliados/programa', $request->file('documentacion'));
                $this->repoPrograma->findByCrear($programa, $fileDocumentacion);

                DB::commit();
                return response()->json(['success' => true, 'message' => 'Programa especial registrado con éxito']);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["success" => false, "message" => "Error al procesar registro de programa especial", "error" => $th->getMessage()], 400);
        }
    }

    public function programaId(Request $request)
    {
        return response()->json($this->repoPrograma->findById($request->id));
    }

    public function consultarPrograma(Request $request)
    {
        return response()->json($this->repoPrograma->findByListar($request));
    }

    public function eliminarProgramaId(Request $request)
    {
        $this->repoPrograma->findByIdDelete($request->id);
        return response()->json(['success' => true, 'message' => 'Registro eliminado con éxito']);
    }

    public function verAdjunto(Request $request)
    {
        $item = $this->repoPrograma->findById($request->id);
        $anioTrabaja = Carbon::parse($item->fecha_registra)->year;
        $path = "afiliados/programa/{$anioTrabaja}/$item->documento_adjunto";

        return $this->repoArchivo->findByObtenerArchivo($path);
    }

    public function filterTipoPrograma($id)
    {
        return TipoProgramaEspecialAfiEntity::where('id_tipo_programa_especial', $id)->first();
    }

    public function saveTipoPrograma(Request $request)
    {
        if ($request->id_tipo_programa_especial != '') {
            $query = TipoProgramaEspecialAfiEntity::where('id_tipo_programa_especial', $request->id_tipo_programa_especial)->first();
            $query->descripcion_programa = $request->descripcion_programa;
            $query->vigente = $request->vigente;
            $query->save();
            return response()->json(['message' => 'Tipo Programa actualizado correctamente'], 200);
        } else {
            $query = TipoProgramaEspecialAfiEntity::where('descripcion_programa', trim($request->descripcion_programa))->first();
            if ($query) {
                return response()->json(['message' => 'Ya existe un programa con la descripcion'], 500);
            }
            TipoProgramaEspecialAfiEntity::create([
                'id_tipo_programa_especial' => $request->id_tipo_programa_especial,
                'descripcion_programa' => $request->descripcion_programa,
                'vigente' => 1
            ]);
            return response()->json(['message' => 'Tipo Programa registrado correctamente'], 200);
        }
    }

    public function updateEstado(Request $request)
    {
        TipoProgramaEspecialAfiEntity::where('id_tipo_programa_especial', $request->id)->update(['vigente' => $request->vigente,]);
        return response()->json(['message' => 'Estado cambiado correctamente'], 200);
    }

    public function findByListarTipos()
    {
        return TipoProgramaEspecialAfiEntity::get();
    }

    public function getByfindProgramasAfiliados(Request $request)
    {
        $dtDatos = [];
        $programa = ProgramaEspecialAfiEntity::with(['tipo_programa'])->where('dni_afiliado', $request->dni)->get();
        $patologia = AfiliadoPadronEntity::where('dni', $request->dni)->first();

        if ($programa->count() > 0) {
            foreach ($programa as $item) {
                $dtDatos[] = [
                    'tipo' => 'Programa Especial',
                    'detalle' => $item?->tipo_programa?->descripcion_programa,
                    'fecha_alta' => $item?->fecha_alta,
                    'fecha_baja' => $item?->fecha_baja,
                    'estado' => $item?->estado_tramite,
                ];
            }
        }

        if ($patologia->patologia != '0' && !empty($patologia->patologia)) {
            $dtDatos[] = [
                'tipo' => 'Patologia',
                'detalle' => $patologia?->patologia ?? null,
                'fecha_alta' => $patologia?->fe_alta,
                'fecha_baja' => $patologia?->fe_baja,
                'estado' => $patologia?->activo,
            ];
        }

        return response()->json($dtDatos, 200);
    }
}
