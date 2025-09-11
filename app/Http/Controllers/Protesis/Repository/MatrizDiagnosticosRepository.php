<?php

namespace App\Http\Controllers\Protesis\Repository;

use App\Models\Protesis\ProtesisMatrizDiagnosticoEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MatrizDiagnosticosRepository
{

    public function save($params)
    {
        $user = Auth::user();
        $fechaAnctual = Carbon::now();

        return ProtesisMatrizDiagnosticoEntity::create([
            'identificador' => $params->identificador,
            'descripcion' => $params->descripcion,
            'fecha_crea' => $fechaAnctual,
            'cod_usuario' => $user->cod_usuario,
            'vigente' => $params->vigente
        ]);
    }

    public function saveId($params)
    {
        $fechaAnctual = Carbon::now();
        $diga = ProtesisMatrizDiagnosticoEntity::find($params->identificador);
        $diga->identificador = $params->identificador;
        $diga->descripcion = $params->descripcion;
        $diga->fecha_actualiza = $fechaAnctual;
        $diga->vigente = $params->vigente;
        $diga->update();
        return $diga;
    }

    public function findByExisteId($id)
    {
        return ProtesisMatrizDiagnosticoEntity::where('identificador', $id)->exists();
    }

    public function findByIdDelete($id)
    {
        return ProtesisMatrizDiagnosticoEntity::find($id)->delete();
    }

    public function findByListPaginate($number)
    {
        return ProtesisMatrizDiagnosticoEntity::limit($number)->get();
    }

    public function findByListIdentificadorLikeAndPaginate($identificador, $number)
    {
        return ProtesisMatrizDiagnosticoEntity::where('identificador', 'LIKE', '%' . $identificador . '%')
            ->limit($number)
            ->get();
    }

    public function findByListDescripcionLikeAndPaginate($descripcion, $number)
    {
        return ProtesisMatrizDiagnosticoEntity::where('descripcion', 'LIKE', '%' . $descripcion . '%')
            ->limit($number)
            ->get();
    }
}
