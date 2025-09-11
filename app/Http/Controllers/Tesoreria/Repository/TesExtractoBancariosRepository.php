<?php

namespace App\Http\Controllers\Tesoreria\Repository;

use App\Models\Tesoreria\TesExtractosBancariosEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TesExtractoBancariosRepository
{

    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findByList($desde,$hasta)
    {
        return TesExtractosBancariosEntity::with(['entidadBancaria'])
        ->whereBetween('fecha_operacion',[$desde,$hasta])
        ->get();
    }
}
