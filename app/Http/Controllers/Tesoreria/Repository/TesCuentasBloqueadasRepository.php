<?php
namespace App\Http\Controllers\Tesoreria\Repository;

use App\Models\Tesoreria\TesCuentasBloqueadasEntity;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TesCuentasBloqueadasRepository
{

    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findBySave($params)
    {
        return TesCuentasBloqueadasEntity::create([
            'id_cuenta_bancaria' => $params->id_cuenta_bancaria,
            'razon_bloqueo' => $params->razon_bloqueo,
            'fecha' => $this->fechaActual,
            'estado' => ($params->estado == '0' ? 'BLOQUEADA' : 'DESBLOQUEADA'),
            'cod_usuario' => $this->user->cod_usuario
        ]);
    }
}
