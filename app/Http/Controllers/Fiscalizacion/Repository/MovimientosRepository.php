<?php

namespace App\Http\Controllers\Fiscalizacion\Repository;

use App\Models\Fiscalizacion\MovimientosEntity;

class MovimientosRepository
{
    public function findAll()
    {
        return MovimientosEntity::all();
    }

    public function findById($id_movimiento)
    {
        return MovimientosEntity::find($id_movimiento);
    }
}
