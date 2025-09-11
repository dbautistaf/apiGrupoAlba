<?php

namespace App\Http\Controllers\Discapacidad;

use App\Http\Controllers\Discapacidad\Repository\DiscaSubsidioRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class DiscaSubsidiosController extends Controller
{

    public function srvImportarArchivosSubsidio(DiscaSubsidioRepository $repo, Request $request)
    {
        DB::beginTransaction();
        try {
            $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
            if ($request->hasFile('archivo')) {
                $archivo = $request->file('archivo');
                $contenido = file_get_contents($archivo->getRealPath());
                $lineas = explode("\n", $contenido);
                $data = array();

                foreach ($lineas as $linea) {
                    $campos = explode('|', $linea);
                    if (isset($campos[1])) {

                        $prestacion = $repo->findByPrestacionesListCuilAndPeriodoAndPractica($campos[3], $campos[4], $this->completarConCeros($campos[6], 3));

                        if (count($prestacion) > 0) {
                            if (count($prestacion) == 1) {
                                $repo->findByUpdateDetalle($prestacion[0]->id_discapacidad_detalle);
                                $repo->findBySave($campos[0], $campos[5], $campos[7], $fechaActual, $prestacion[0]->id_discapacidad_detalle);
                                $repo->findByUpdateDisca($campos[0], '1', $prestacion[0]->id_discapacidad);
                            } else {
                                $monto_solicitado = 0;
                                foreach ($prestacion as $key) {
                                    $monto_solicitado += $key->disca->monto_solicitado;
                                }

                                if ($campos[7] == $monto_solicitado) {
                                    foreach ($prestacion as $key) {
                                        $repo->findByUpdateDetalle($key->id_discapacidad_detalle);
                                        $repo->findBySave($campos[0], $key->disca->monto_solicitado, $key->disca->monto_solicitado, $fechaActual, $key->id_discapacidad_detalle);
                                        $repo->findByUpdateDisca($campos[0], '1', $prestacion[0]->id_discapacidad);
                                    }
                                } else {
                                    $montoTotalSolicitado = 0;
                                    $montoReconocido = $campos[7];

                                    foreach ($prestacion as $key) {
                                        $montoTotalSolicitado += $key->disca->monto_solicitado;
                                    }

                                    foreach ($prestacion as $key) {
                                        $porcentaje = $key->disca->monto_solicitado / $montoTotalSolicitado * 100;
                                        $montoSubsidiadoItem = $montoReconocido * $porcentaje / 100;

                                        $repo->findByUpdateDetalle($key->id_discapacidad_detalle);
                                        $repo->findBySave($campos[0], $key->disca->monto_solicitado, $montoSubsidiadoItem, $fechaActual, $key->id_discapacidad_detalle);
                                        $repo->findByUpdateDisca($campos[0], '1', $prestacion[0]->id_discapacidad);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            DB::commit();
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message" => $th->getMessage()], 500);
        }
    }

    public static function completarConCeros($cadena, $longitud)
    {
        $cerosNecesarios = $longitud - strlen($cadena);
        if ($cerosNecesarios > 0) {
            $cadena = str_repeat('0', $cerosNecesarios) . $cadena;
        }

        return $cadena;
    }
}
