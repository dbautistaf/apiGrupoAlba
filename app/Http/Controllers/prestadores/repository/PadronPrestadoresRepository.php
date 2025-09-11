<?php

namespace App\Http\Controllers\prestadores\repository;

use App\Models\prestadores\PrestadorEntity;
use App\Models\prestadores\TipoInputacionesContablesEntity;

class PadronPrestadoresRepository
{

    public function findByListCuitLike($search, $paginate)
    {
        return PrestadorEntity::with(['datosBancarios'])
            ->where('cuit', 'LIKE',   $search . '%')
            ->orderBy('razon_social')
            ->limit($paginate)
            ->get();
    }

    public function findByListRazonSocialLike($search, $paginate)
    {
        return PrestadorEntity::with(['datosBancarios'])
            ->where('nombre_fantasia', 'LIKE',  $search . '%')
            ->orWhere('razon_social', 'LIKE',  $search . '%')
            ->orderBy('razon_social')
            ->limit($paginate)
            ->get();
    }

    public function findByListPaginateTop($paginate)
    {
        return PrestadorEntity::with(['datosBancarios'])
            ->orderBy('razon_social')
            ->limit($paginate)
            ->get();
    }

    public function findByListarTipoImputacion($filters)
    {
        $sql = TipoInputacionesContablesEntity::with([]);
        if (!is_null($filters->search)) {
            $sql->where('imputacion', 'LIKE', "%$filters->search%");
            $sql->orWhere('codigo', 'LIKE', "%$filters->search%");
        }
        return $sql->get();
    }
}
