<?php

namespace App\Http\Controllers\Protesis\Repository;

use App\Models\Protesis\ProtesisCategoriaProductoEntity;

class CategoriaProductosRepository
{

    public function findByListAlls()
    {
        return ProtesisCategoriaProductoEntity::orderByDesc('descripcion')
            ->get();
    }
}
