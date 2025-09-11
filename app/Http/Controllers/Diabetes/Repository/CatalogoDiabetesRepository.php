<?php

namespace App\Http\Controllers\Diabetes\Repository;

use App\Models\Diabetes\MedicamentosDiabetesEntity;
use App\Models\Diabetes\TipoDiabetesEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CatalogoDiabetesRepository
{
    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findByListTipoDiabetes()
    {
        return TipoDiabetesEntity::get();
    }

    public function findByListMedicamentos()
    {
        return MedicamentosDiabetesEntity::where('vigente', '1')->get();
    }

    public function findByListMedicamentosMatriz($search)
    {
        $sql = MedicamentosDiabetesEntity::whereIn('vigente', ['1', '0']);
        if (!is_null($search)) {
            $sql->where('nombre_medicamento', 'LIKE', "%$search%");
        }
      return  $sql->get();
    }

    public function findByCrearMedicamento($params)
    {
        return MedicamentosDiabetesEntity::create([
            'nombre_medicamento' => strtoupper($params->nombre_medicamento),
            'presentacion' => $params->presentacion,
            'unidad' => $params->unidad,
            'vigente' => $params->vigente,
            'cod_usuario' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual
        ]);
    }

    public function findByUpdateMedicamento($params)
    {
        $tipo =  MedicamentosDiabetesEntity::find($params->id_medicamento);
        $tipo->nombre_medicamento = strtoupper($params->nombre_medicamento);
        $tipo->presentacion = $params->presentacion;
        $tipo->unidad = $params->unidad;
        $tipo->vigente = $params->vigente;
        $tipo->cod_usuario_modifica  = $this->user->cod_usuario;
        $tipo->fecha_modifica = $this->fechaActual;
        $tipo->update();
        return $tipo;
    }

    public function findByDeleteMedicamento($params)
    {
        $tipo =  MedicamentosDiabetesEntity::find($params->id_medicamento);
        $tipo->vigente = $params->vigente;
        $tipo->update();
        return $tipo;
    }
}
