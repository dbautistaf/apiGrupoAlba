<?php

namespace App\Http\Controllers\Protesis\Repository;

use App\Models\Protesis\DetalleCotizacionProtesisEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DetalleCotizacionRepository
{

    private $user;
    private $fechaActual;
    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findBySaveDetalleCotizacion($detalle, $idProtesis)
    {
        foreach ($detalle as $key) {
            foreach ($key->productos as $value) {
                if (!is_null($value->id_cotizacion)) {
                    $item =  DetalleCotizacionProtesisEntity::find($value->id_cotizacion);
                    $item->cantidad_autorizada = $value->cantidad_autoriza;
                    $item->monto_cotiza = $value->monto_cotiza;
                    $item->importe_total = $value->importe_total;
                    $item->observaciones = $value->observaciones;
                    $item->update();
                } else {
                    DetalleCotizacionProtesisEntity::create([
                        'id_detalle_producto_licitacion' => $value->id_detalle_producto_licitacion,
                        'cantidad_autorizada' =>  $value->cantidad_autoriza,
                        'monto_cotiza' =>  $value->monto_cotiza,
                        'importe_total' =>  $value->importe_total,
                        'observaciones' =>  $value->observaciones,
                        'id_solicitud'  => $value->id_solicitud,
                        'id_protesis' => $idProtesis,
                        'fecha_registra' =>  $this->fechaActual,
                        'cod_usuario' => $this->user->cod_usuario
                    ]);
                }
            }
        }
    }

    public function findByDetalleCotizacion($idProtesis)
    {
        return DB::table('vw_detalle_cotizacion_licitacion')
            ->where('id_protesis', $idProtesis)->get();
    }
}
