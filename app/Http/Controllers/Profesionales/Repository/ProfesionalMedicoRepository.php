<?php
namespace App\Http\Controllers\Profesionales\Repository;

use App\Models\prestadores\PrestadorMedicosEntity;
use Auth;
use Carbon\Carbon;

class ProfesionalMedicoRepository
{

    private $fechaActual;
    private $user;
    public function __construct()
    {
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        $this->user = Auth::user();
    }

    public function findByRegistroRapido($params)
    {
        return PrestadorMedicosEntity::create([
            'dni' => '00000000',
            'apellidos_nombres' => $params->nombres,
            'numero_matricula' => '000000',
            'vigente' => '1',
            'cod_usuario_registra' => $this->user->cod_usuario,
            'fecha_alta' => $this->fechaActual
        ]);
    }
}
