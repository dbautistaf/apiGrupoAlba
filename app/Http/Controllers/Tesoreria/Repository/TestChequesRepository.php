<?php

namespace App\Http\Controllers\Tesoreria\Repository;

use App\Models\Tesoreria\TestChequesEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestChequesRepository
{

    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findByCrear($params, $archivo)
    {
        return TestChequesEntity::create([
            'id_cuenta_bancaria' => $params->id_cuenta_bancaria,
            'tipo_cheque' => $params->tipo_cheque,
            'numero_cheque' => $params->numero_cheque,
            'monto' => $params->monto,
            'fecha_emision' => $params->fecha_emision,
            'fecha_vencimiento' => $params->fecha_vencimiento,
            'tipo' => $params->tipo,
            'estado' => $params->estado,
            'descripcion' => $params->descripcion,
            'beneficiario' => $params->beneficiario,
            'cod_usuario_registra' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual,
            'archivo_adjunto' => $archivo,
            'numero_cheque_anterior' => $params->numero_cheque_anterior
        ]);
    }

    public function findByUpdate($params, $archivo, $id)
    {
        $cheque = TestChequesEntity::find($id);
        $cheque->id_cuenta_bancaria = $params->id_cuenta_bancaria;
        $cheque->tipo_cheque = $params->tipo_cheque;
        $cheque->numero_cheque = $params->numero_cheque;
        $cheque->monto = $params->monto;
        $cheque->fecha_emision = $params->fecha_emision;
        $cheque->fecha_vencimiento = $params->fecha_vencimiento;
        $cheque->beneficiario = $params->beneficiario;
        $cheque->tipo = $params->tipo;
        $cheque->estado = $params->estado;
        $cheque->descripcion = $params->descripcion;
        $cheque->cod_usuario_modifica = $this->user->cod_usuario;
        $cheque->fecha_modificia = $this->fechaActual;
        $cheque->numero_cheque_anterior = $params->numero_cheque_anterior;

        if ($archivo != null) {
            $cheque->archivo_adjunto = $archivo;
        }
        $cheque->update();
    }

    public function findByList($desde, $hasta)
    {
        return TestChequesEntity::with(['cuenta'])
            ->whereBetween(DB::raw('DATE(fecha_registra)'), [$desde, $hasta])
            ->orderByDesc('id_cheque')
            ->get();
    }

    public function findById($id)
    {
        return TestChequesEntity::find($id);
    }

    public function findByExistsNumCheque($numcheque)
    {
        return TestChequesEntity::where('numero_cheque', $numcheque)
            ->where('estado', 'ACTIVO')->exists();
    }

    public function findByDesactivarCheque($numcheque, $estado = "REEMPLAZADO")
    {
        $cheque = TestChequesEntity::where('numero_cheque', $numcheque)->first();
        $cheque->estado = $estado;
        $cheque->update();
        return $cheque;
    }
}
