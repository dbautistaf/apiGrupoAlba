<?php

namespace App\Http\Controllers\matrizPracticas\Repository;

use App\Http\Controllers\matrizPracticas\Dto\MatrizGenericoPracticasDto;
use Illuminate\Support\Facades\DB;

class MatrizPracticasRepository
{

    public function findByListCodPracticaLike($codPractica)
    {
        $rows = DB::select("SELECT nomenclador, seccion, padre, codigo_practica, nombre_practica,id_nomenclador,
            id_seccion,id_padre,id_identificador_practica FROM vw_matriz_practicas
        WHERE codigo_practica LIKE ? ORDER BY codigo_practica ASC LIMIT 20 ", [$codPractica . '%']);

        return $this->getBuilderGenerico($rows);
    }

    public function findByListPracticaLike($practica)
    {
        $rows = DB::select("SELECT nomenclador, seccion, padre, codigo_practica, nombre_practica,id_nomenclador,
            id_seccion,id_padre,id_identificador_practica FROM vw_matriz_practicas
        WHERE nombre_practica LIKE ? ORDER BY codigo_practica ASC LIMIT 20 ", [$practica . '%']);
        return $this->getBuilderGenerico($rows);
    }

    public function findByListIdNomencladorAndIdSeccionAndCodPracticaLike($idNomenclador, $idSseccion, $codPractica)
    {
        $rows = DB::select("SELECT nomenclador, seccion, padre, codigo_practica, nombre_practica,id_nomenclador,
            id_seccion,id_padre,id_identificador_practica FROM vw_matriz_practicas
        WHERE id_nomenclador = ? AND id_seccion = ? AND codigo_practica LIKE ? ORDER BY codigo_practica ASC ", [$idNomenclador, $idSseccion,  $codPractica . '%']);

        return $this->getBuilderGenerico($rows);
    }
    public function findByListCodPracticaLikeAndIdNomenclador($codPractica, $idNomenclador)
    {
        $rows = DB::select("SELECT nomenclador, seccion, padre, codigo_practica, nombre_practica,id_nomenclador,
            id_seccion,id_padre,id_identificador_practica FROM vw_matriz_practicas
        WHERE id_nomenclador = ? AND codigo_practica LIKE ? ORDER BY codigo_practica ASC ", [$idNomenclador,  $codPractica . '%']);

        return $this->getBuilderGenerico($rows);
    }
    public function findByListNombrePracticaLike($nombrePractica)
    {
        $rows = DB::select("SELECT nomenclador, seccion, padre, codigo_practica, nombre_practica,id_nomenclador,
            id_seccion,id_padre,id_identificador_practica FROM vw_matriz_practicas
        WHERE nombre_practica LIKE ? ORDER BY codigo_practica ASC LIMIT 20  ", [$nombrePractica . '%']);

        return $this->getBuilderGenerico($rows);
    }

    public function findByListIdNomenclador($idNomenclador)
    {
        $rows = DB::select("SELECT nomenclador, seccion, padre, codigo_practica, nombre_practica,id_nomenclador,
            id_seccion,id_padre,id_identificador_practica FROM vw_matriz_practicas
        WHERE id_nomenclador = ? ORDER BY codigo_practica ASC ", [$idNomenclador]);

        return $this->getBuilderGenerico($rows);
    }

    public function findByListIdNomencladorAndIdSeccion($idNomenclador, $idSeccion)
    {
        $rows = DB::select("SELECT nomenclador, seccion, padre, codigo_practica, nombre_practica,id_nomenclador,
            id_seccion,id_padre,id_identificador_practica FROM vw_matriz_practicas
        WHERE id_nomenclador = ? AND id_seccion = ? ORDER BY codigo_practica ASC ", [$idNomenclador, $idSeccion]);

        return $this->getBuilderGenerico($rows);
    }

    public function findByListIdSeccion($idSeccion)
    {
        $rows = DB::select("SELECT nomenclador, seccion, padre, codigo_practica, nombre_practica,id_nomenclador,
            id_seccion,id_padre,id_identificador_practica FROM vw_matriz_practicas
        WHERE id_seccion = ? ORDER BY codigo_practica ASC ", [$idSeccion]);

        return $this->getBuilderGenerico($rows);
    }

    public function findByListIdPadre($idPadre)
    {
        $rows = DB::select("SELECT nomenclador, seccion, padre, codigo_practica, nombre_practica,id_nomenclador,
            id_seccion,id_padre,id_identificador_practica FROM vw_matriz_practicas
        WHERE id_padre = ? ORDER BY codigo_practica ASC ", [$idPadre]);

        return $this->getBuilderGenerico($rows);
    }

    public function findByListIdNomencladorAndIdSeccionAndIdPadre($idNomenclador, $idSeccion, $idPadre)
    {
        $rows = DB::select("SELECT nomenclador, seccion, padre, codigo_practica, nombre_practica,id_nomenclador,
            id_seccion,id_padre,id_identificador_practica FROM vw_matriz_practicas
        WHERE id_nomenclador = ? AND id_seccion = ? AND id_padre = ? ORDER BY codigo_practica ASC ", [$idNomenclador, $idSeccion, $idPadre]);

        return $this->getBuilderGenerico($rows);
    }

    public function findByListCodPracticaBetweenAndNomenclador($codPracticaDesde, $codPracticaHasta, $idNomenclador)
    {
        $rows = DB::select("SELECT nomenclador, seccion, padre, codigo_practica, nombre_practica,id_nomenclador,
            id_seccion,id_padre,id_identificador_practica FROM vw_matriz_practicas
        WHERE id_nomenclador = ? AND codigo_practica BETWEEN ? AND ? ORDER BY codigo_practica ASC", [$idNomenclador, $codPracticaDesde, $codPracticaHasta]);

        return $this->getBuilderGenerico($rows);
    }

    public function findByListCodPracticaBetweenAndNomencladorAndSeccion($codPracticaDesde, $codPracticaHasta, $idNomenclador, $idSeccion)
    {
        $rows = DB::select("SELECT nomenclador, seccion, padre, codigo_practica, nombre_practica,id_nomenclador,
            id_seccion,id_padre,id_identificador_practica FROM vw_matriz_practicas
        WHERE id_nomenclador = ? AND id_seccion = ? AND codigo_practica BETWEEN ? AND ? ORDER BY codigo_practica ASC", [$idNomenclador, $idSeccion, $codPracticaDesde, $codPracticaHasta]);

        return $this->getBuilderGenerico($rows);
    }

    public function findByListCodPracticaBetweenAndNomencladorAndSeccionAndPadre($codPracticaDesde, $codPracticaHasta, $idNomenclador, $idSeccion, $idPadre)
    {
        $rows = DB::select("SELECT nomenclador, seccion, padre, codigo_practica, nombre_practica,id_nomenclador,
            id_seccion,id_padre,id_identificador_practica FROM vw_matriz_practicas
        WHERE id_nomenclador = ? AND id_seccion = ? AND id_padre = ?  AND codigo_practica BETWEEN ? AND ? ORDER BY codigo_practica ASC", [$idNomenclador, $idSeccion, $idPadre, $codPracticaDesde, $codPracticaHasta]);

        return $this->getBuilderGenerico($rows);
    }

    public function findByListCodPracticaBetweenAndNomencladorAndPadre($codPracticaDesde, $codPracticaHasta, $idNomenclador, $idPadre)
    {
        $rows = DB::select("SELECT nomenclador, seccion, padre, codigo_practica, nombre_practica,id_nomenclador,
            id_seccion,id_padre,id_identificador_practica FROM vw_matriz_practicas
        WHERE id_nomenclador = ? AND id_padre = ?  AND codigo_practica BETWEEN ? AND ? ORDER BY codigo_practica ASC", [$idNomenclador, $idPadre, $codPracticaDesde, $codPracticaHasta]);

        return $this->getBuilderGenerico($rows);
    }

    public function findByListCodPracticaBetween($codPracticaDesde, $codPracticaHasta)
    {
        $rows = DB::select("SELECT nomenclador, seccion, padre, codigo_practica, nombre_practica,id_nomenclador,
            id_seccion,id_padre,id_identificador_practica FROM vw_matriz_practicas
        WHERE  codigo_practica BETWEEN ? AND ? ORDER BY codigo_practica ASC", [$codPracticaDesde, $codPracticaHasta]);

        return $this->getBuilderGenerico($rows);
    }

    public function findByListNomencladorAndPadre($idNomenclador,  $idPadre)
    {
        $rows = DB::select("SELECT nomenclador, seccion, padre, codigo_practica, nombre_practica,id_nomenclador,
            id_seccion,id_padre,id_identificador_practica FROM vw_matriz_practicas
        WHERE id_nomenclador = ?   AND id_padre = ?    ORDER BY codigo_practica ASC", [$idNomenclador, $idPadre]);

        return $this->getBuilderGenerico($rows);
    }

    public function findByPracticaLikeLimit($practica, $limit)
    {
        return DB::table('vw_matriz_practicas')
            ->select('nomenclador', 'seccion', 'padre', 'codigo_practica', 'nombre_practica', 'id_nomenclador', 'id_seccion', 'id_padre', 'id_identificador_practica')
           ->where(function ($query) use ($practica){
            $query->where('codigo_practica', 'LIKE', $practica . '%')
            ->orWhere('nombre_practica', 'LIKE', '%'. $practica . '%');
           })
            ->orderBy('codigo_practica')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                return new MatrizGenericoPracticasDto(
                    $row->nomenclador,
                    $row->seccion,
                    $row->padre,
                    $row->codigo_practica,
                    $row->nombre_practica,
                    $row->id_nomenclador,
                    $row->id_seccion,
                    $row->id_padre,
                    $row->id_identificador_practica,
                    true
                );
            })
            ->unique('codigo_practica')
            ->values();
    }
    public function findByCodigoPracticaLikeLimit($practica, $limit)
    {
        return DB::table('vw_matriz_practicas')
            ->select('nomenclador', 'seccion', 'padre', 'codigo_practica', 'nombre_practica', 'id_nomenclador', 'id_seccion', 'id_padre', 'id_identificador_practica')
            ->where('codigo_practica', 'LIKE', $practica . '%')
            ->orderBy('codigo_practica')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                return new MatrizGenericoPracticasDto(
                    $row->nomenclador,
                    $row->seccion,
                    $row->padre,
                    $row->codigo_practica,
                    $row->nombre_practica,
                    $row->id_nomenclador,
                    $row->id_seccion,
                    $row->id_padre,
                    $row->id_identificador_practica,
                    true
                );
            })
            ->unique('codigo_practica')
            ->values();
    }
    public function findByLimit($limit)
    {
        return DB::table('vw_matriz_practicas')
            ->select('nomenclador', 'seccion', 'padre', 'codigo_practica', 'nombre_practica', 'id_nomenclador', 'id_seccion', 'id_padre', 'id_identificador_practica')
            ->orderBy('codigo_practica')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                return new MatrizGenericoPracticasDto(
                    $row->nomenclador,
                    $row->seccion,
                    $row->padre,
                    $row->codigo_practica,
                    $row->nombre_practica,
                    $row->id_nomenclador,
                    $row->id_seccion,
                    $row->id_padre,
                    $row->id_identificador_practica,
                    true
                );
            })
            ->unique('codigo_practica')
            ->values();
    }


    private function getBuilderGenerico($rows)
    {
        return  collect($rows)
            ->map(function ($row) {
                return new MatrizGenericoPracticasDto(
                    $row->nomenclador,
                    $row->seccion,
                    $row->padre,
                    $row->codigo_practica,
                    $row->nombre_practica,
                    $row->id_nomenclador,
                    $row->id_seccion,
                    $row->id_padre,
                    $row->id_identificador_practica,
                    true
                );
            })
            ->unique('codigo_practica')->values()->toArray();;
    }
}
