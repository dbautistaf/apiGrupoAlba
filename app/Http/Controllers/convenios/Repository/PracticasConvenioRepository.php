<?php
namespace App\Http\Controllers\convenios\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PracticasConvenioRepository
{

    public function findByListPracticasPrestador($params)
    {
        return DB::select("SELECT * FROM vw_convenio_practicas_prestador where vigente = '1' AND cod_prestador = ?", [$params->cod_prestador]);
    }

    public function findByListCodigoPracticasPrestador($params)
    {
        return DB::select("SELECT * FROM vw_convenio_practicas_prestador where vigente = '1' AND cod_prestador = ? and codigo_practica LIKE  ?", [$params->cod_prestador, $params->codigo.'%']);
    }

    public function findByListDescripcionPracticasPrestador($params)
    {
        return DB::select("SELECT * FROM vw_convenio_practicas_prestador where vigente = '1' AND cod_prestador = ? and nombre_practica LIKE  ?", [$params->cod_prestador, $params->descripcion.'%']);
    }

    public function findByCostoPracticaConvenio($id_practica, $periodo,$cod_convenio, $cod_prestador)
    {
        $fechaCorte = Carbon::parse($periodo)->endOfMonth();
        $fechaCorte->toDateString();
        return DB::select("SELECT *
                FROM vw_convenio_practicas_historial_pago
                WHERE id_identificador_practica = ? AND cod_convenio = ? AND cod_prestador = ?
                AND (
                    (fecha_inicio <= ? AND fecha_fin >= ?)
                )", [$id_practica,$cod_convenio,$cod_prestador, $fechaCorte, $periodo . '-01']);
    }
}
