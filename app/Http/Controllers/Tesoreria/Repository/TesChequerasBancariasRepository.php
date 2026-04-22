<?php

namespace App\Http\Controllers\Tesoreria\Repository;

use App\Models\Tesoreria\ChequerasCuentasBancariasEntity;
use App\Models\Tesoreria\TipoChequerasEntity;
use Illuminate\Support\Facades\Auth;

class TesChequerasBancariasRepository
{
    private  $user;
    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function findByLisTipoChequeras()
    {
        return TipoChequerasEntity::where('estado', '1')
            ->get();
    }

    public function findByListChequeras($filter)
    {
        $sql = ChequerasCuentasBancariasEntity::with(['tipo'])
            ->where('id_cuenta_bancaria', $filter->id_cuenta_bancaria);

        if (!is_null($filter->estado)) {
            $sql->where('estado', $filter->estado);
        }

        return $sql->get();
    }

    public function findByCreate($chequera)
    {
        return ChequerasCuentasBancariasEntity::create([
            'id_cuenta_bancaria' => $chequera->id_cuenta_bancaria,
            'id_tipo_chequera' => $chequera->id_tipo_chequera,
            'nombre_chequera' => $chequera->nombre_chequera,
            'prefijo_chequera' => $chequera->prefijo_chequera,
            'nro_inicial' => $chequera->nro_inicial,
            'nro_final' => $chequera->nro_final,
            'nro_actual' => $chequera->nro_actual,
            'estado' => $chequera->estado,
            'cod_usuario' => $this->user->cod_usuario
        ]);
    }

    public function findByModificar($chequera)
    {
        $chequeraId = ChequerasCuentasBancariasEntity::find($chequera->id_chequera);

        if (!is_null($chequeraId)) {
            $chequeraId->id_cuenta_bancaria = $chequera->id_cuenta_bancaria;
            $chequeraId->id_tipo_chequera = $chequera->id_tipo_chequera;
            $chequeraId->nombre_chequera = $chequera->nombre_chequera;
            $chequeraId->prefijo_chequera = $chequera->prefijo_chequera;
            $chequeraId->nro_inicial = $chequera->nro_inicial;
            $chequeraId->nro_final = $chequera->nro_final;
            $chequeraId->nro_actual = $chequera->nro_actual;
            $chequeraId->estado = $chequera->estado;
            $chequeraId->cod_usuario_modifica = $this->user->cod_usuario;
            $chequeraId->update();
        }
    }

    public function findByIncrementarChequera($idChequera)
    {
        $chequera = ChequerasCuentasBancariasEntity::find($idChequera);
        $chequera->nro_actual = $chequera->nro_actual + 1;
        $chequera->update();
        return $chequera;
    }

    public function findByChequeraTope($idChequera)
    {
        $chequera = ChequerasCuentasBancariasEntity::find($idChequera);
        $numero = $chequera->nro_actual + 1;
        if ($numero > $chequera->nro_final) {
            return true;
        }
        return false;
    }

    public function findByNumeroChequera($idChequera)
    {
        $chequera = ChequerasCuentasBancariasEntity::find($idChequera);
        return $chequera->prefijo_chequera . '-' . ($chequera->nro_actual + 1);
    }

    public function findByEliminar($idChequera)
    {
        return  ChequerasCuentasBancariasEntity::find($idChequera)->delete();
    }

    public function findByUpdateEstado($idChequera, $estado)
    {
        return  ChequerasCuentasBancariasEntity::find($idChequera)
            ->update(['estado' => $estado, 'cod_usuario_modifica' => $this->user->cod_usuario]);
    }
}
