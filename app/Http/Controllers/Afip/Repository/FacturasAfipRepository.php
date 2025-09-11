<?php

namespace App\Http\Controllers\Afip\Repository;

use App\Models\Afip\FacturasAfipEntity;

class FacturasAfipRepository
{

    public function findByListTodos($params)
    {
        $query = FacturasAfipEntity::with([]);
        $query->whereBetween('fecha', [$params->desde, $params->hasta]);
        if (!is_null($params->search)) {
            $query->where(function ($sql) use ($params) {
                $sql->where('nro_doc_receptor', 'LIKE', ["$params->search%"])
                    ->orWhere('denominacion_receptor', 'LIKE', ["$params->search%"]);
            });
        }
        $query->orderByDesc('fecha');
        $query->limit(3500);
        return $query->get();
    }
}
