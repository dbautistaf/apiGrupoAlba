<?php

namespace App\Http\Controllers\Seguridad\Repository;

use App\Models\PerfilModelo;
use App\Models\User;

class PerfilesRepository
{

    public function findByListAlls()
    {
        return PerfilModelo::orderBy('nombre_perfil')
        ->get();
    }

    public function findByCrear($params)
    {
        return PerfilModelo::create([
            'nombre_perfil' => $params->nombre_perfil,
            'estado' => $params->estado
        ]);
    }

    public function findByActualizarId($params)
    {
        $perfil =  PerfilModelo::find($params->cod_perfil);
        $perfil->nombre_perfil = $params->nombre_perfil;
        $perfil->estado = $params->estado;
        $perfil->update();
        return $perfil;
    }

    public function findByEliminarId($id)
    {
        return  PerfilModelo::find($id)->delete();
    }

    public function findByExistePerfilAsignadoId($id)
    {
        return User::where('cod_perfil', $id)->exists();
    }
}
