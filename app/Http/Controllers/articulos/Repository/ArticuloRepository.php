<?php

namespace App\Http\Controllers\articulos\Repository;

use Illuminate\Support\Facades\DB;

class ArticuloRepository
{

    public function findByListArticuloLike($search, $limit)
    {
        return DB::select('SELECT * FROM vw_matriz_articulos WHERE articulo LIKE ? ORDER BY articulo ASC LIMIT ? ', [$search,$limit]);
    }

    public function findByListArticuloVigente($estado, $limit)
    {
        return DB::select('SELECT * FROM vw_matriz_articulos WHERE vigente = ?  ORDER BY articulo ASC LIMIT ? ', [$estado, $limit]);
    }

    public function findByListArticuloAlls($limit)
    {
        return DB::select('SELECT * FROM vw_matriz_articulos ORDER BY articulo ASC LIMIT ? ', [$limit]);
    }

    public function findByListArticuloId($id)
    {
        return DB::select('SELECT * FROM vw_matriz_articulos WHERE id_articulo = ? ', [$id]);
    }

}
