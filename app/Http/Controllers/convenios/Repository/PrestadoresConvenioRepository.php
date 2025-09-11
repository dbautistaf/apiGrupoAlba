<?php

namespace App\Http\Controllers\convenios\Repository;

use Illuminate\Support\Facades\DB;

class PrestadoresConvenioRepository
{
    public function findByListPrestadores($id)
    {
        return DB::select("SELECT * FROM vw_prestadores_convenio WHERE cod_convenio = ? ", [$id]);
    }
}
