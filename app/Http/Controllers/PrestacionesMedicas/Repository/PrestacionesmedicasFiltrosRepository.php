<?php

namespace App\Http\Controllers\PrestacionesMedicas\Repository;

use App\Models\PrestacionesMedicas\PrestacionesPracticaLaboratorioEntity;
use App\Models\PrestacionesMedicas\PrestacionMedicaFile;
use Illuminate\Support\Facades\Auth;

class PrestacionesmedicasFiltrosRepository
{

    private $user;
    private $allRelations;
    public function __construct()
    {
        $this->user = Auth::user();
        $this->allRelations = ["detalle", "detalle.practica", "estadoPrestacion", "afiliado", "afiliado.obrasocial", "usuario", "prestador", "profesional", "datosTramite", "datosTramite.tramite", "datosTramite.prioridad", "datosTramite.obrasocial", "documentacion"];
    }

    public function findByListFechaRegistraBetweenAndDniAfiliado($desde, $hasta, $dni, $tramite)
    {
        $query = PrestacionesPracticaLaboratorioEntity::with($this->allRelations)
            ->whereBetween('fecha_registra', [$desde, $hasta])
            ->where('dni_afiliado', $dni);

        if (!is_null($tramite)) {
            $query->where('numero_tramite', 'like', $tramite . '%');
        }

        $results = $query->orderByDesc('cod_prestacion')->get();

        return $results;
    }

    public function findByListFechaRegistraBetweenAndCuilAfiliado($desde, $hasta, $cuil, $tramite)
    {
        $query = PrestacionesPracticaLaboratorioEntity::with($this->allRelations)
            ->whereBetween('fecha_registra', [$desde, $hasta])
            ->whereHas('afiliado', function ($query) use ($cuil) {
                $query->where('cuil_benef', $cuil);
            });

        if (!is_null($tramite)) {
            $query->where('numero_tramite', 'like', $tramite . '%');
        }
        $results = $query->orderByDesc('cod_prestacion')->get();
        return $results;
    }

    public function findByListFechaRegistraBetweenAndDniAfiliadoLike($desde, $hasta, $dni, $tramite)
    {
        return PrestacionesPracticaLaboratorioEntity::with($this->allRelations)
            ->whereBetween('fecha_registra', [$desde, $hasta])
            ->where('dni_afiliado', 'LIKE', $dni . '%');

        if (!is_null($tramite)) {
            $query->where('numero_tramite', 'like', $tramite . '%');
        }
        $results = $query->orderByDesc('fecha_registra')->get();
        return $results;
    }

    public function findByListFechaRegistraBetweenAndNombresAfiliadoLike($desde, $hasta, $nombres, $tramite)
    {
        return PrestacionesPracticaLaboratorioEntity::with($this->allRelations)
            ->whereBetween('fecha_registra', [$desde, $hasta])
            ->whereHas('afiliado', function ($query) use ($nombres) {
                $query->where(function ($q) use ($nombres) {
                    $q->where('apellidos', 'like', '%' . $nombres . '%')
                        ->orWhere('nombre', 'like', '%' . $nombres . '%');
                });
            });

        if (!is_null($tramite)) {
            $query->where('numero_tramite', 'like', $tramite . '%');
        }
        $results = $query->orderByDesc('fecha_registra')->get();
        return $results;
    }

    public function findByListEstado($estado)
    {
        return PrestacionesPracticaLaboratorioEntity::with($this->allRelations)
            ->where('cod_tipo_estado', $estado)
            ->orderByDesc('fecha_registra')
            ->get();
    }

    public function findByListFechaRegistraBetweenAndLimit($desde, $hasta, $limit, $tramite)
    {
        $query = PrestacionesPracticaLaboratorioEntity::with($this->allRelations)
            ->whereBetween('fecha_registra', [$desde, $hasta]);
        if (!is_null($tramite)) {
            $query->where('numero_tramite', 'like', $tramite . '%');
        }
        $results = $query
            ->orderByRaw("
                CASE 
                    WHEN fecha_modifica IS NULL OR fecha_modifica = '' 
                    THEN fecha_registra 
                    ELSE fecha_modifica 
                    END DESC
                ")
            ->orderBy('cod_prestacion', 'desc')
            ->limit($limit)
            ->get();
        return $results;
    }

    public function findById($id)
    {
        $anioTrabajo = date('Y');
        $prestacion = PrestacionesPracticaLaboratorioEntity::with($this->allRelations)->find($id);

        /* $listFile = PrestacionMedicaFile::where('cod_prestacion', $prestacion->cod_prestacion)->get();

        $files = $listFile->map(function ($file) use ($anioTrabajo) {
            $file->url = url("/storage/prestaciones/{$anioTrabajo}/{$file->archivo}");
            return $file;
        });

        $prestacion->setRelation('url', $files);
 */
        return $prestacion;
    }

    public function findByDeleteId($id)
    {
        //return PrestacionesPracticaLaboratorioEntity::find($id)->delete();

        $registro = PrestacionesPracticaLaboratorioEntity::find($id);

        if ($registro) {
            $registro->cod_tipo_estado = 7;
            $registro->save();
        }
    }

    public function findByListDniAfiliado($dni)
    {
        return PrestacionesPracticaLaboratorioEntity::with($this->allRelations)
            ->where('dni_afiliado', $dni)
            ->orderByDesc('cod_prestacion')
            ->get();
    }

    public function findByListAutorizacionLimit($shared)
    {
        $query = PrestacionesPracticaLaboratorioEntity::orderBy('cod_prestacion', 'desc');

        if (!empty($shared)) {
            $query->where('numero_tramite', 'like', "%{$shared}%");
        } else {
            $query->limit(20);
        }

        return $query->get();
    }

    public function findByListAutorizacionIds($ids)
    {
        return PrestacionesPracticaLaboratorioEntity::whereIn('cod_prestacion', $ids)->get();
    }
}
