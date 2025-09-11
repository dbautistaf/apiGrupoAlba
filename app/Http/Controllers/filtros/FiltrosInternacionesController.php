<?php

namespace App\Http\Controllers\filtros;



use App\Models\CategoriaInternacionEntity;
use App\Models\TipoDiagnosticoInternacionEntity;
use App\Models\TipoEgresoInternacionEntity;
use App\Models\TipoEstadoPrestacionEntity;
use App\Models\TipoFacturacionInternacionEntity;
use App\Models\TipoHabitacionEntity;
use App\Models\TipoInternacionEntity;
use App\Models\TipoPrestacionEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FiltrosInternacionesController extends Controller
{

    public function listEstadoInternacion(){
        return TipoEstadoPrestacionEntity::get();
    }


}
