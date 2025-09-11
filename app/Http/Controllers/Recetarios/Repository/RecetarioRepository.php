<?php

namespace App\Http\Controllers\Recetarios\Repository;

use App\Models\RecetasModelo;

class RecetarioRepository
{

    public function findByListLimit($desde, $hasta, $limit)
    {
        return RecetasModelo::with('Afiliado', 'Farmacia')
            ->whereBetween('fecha_carga', [$desde, $hasta])
            ->orderByDesc('fecha_carga')
            ->limit($limit)
            ->get();
    }

    public function findByListFarmaciaAndFecha($search, $desde, $hasta)
    {
        return RecetasModelo::with('Afiliado', 'Farmacia')
            ->where(function ($query) use ($search) {
                $query->whereHas('Afiliado', function ($queryAfiliado) use ($search) {
                    $queryAfiliado->where('nombre', 'LIKE', "$search%")->orWhere('dni', 'LIKE', "$search%");
                })->orWhereHas('Farmacia', function ($queryFarmacia) use ($search) {
                    $queryFarmacia->where('razon_social', 'LIKE', "$search%");
                });
            })
            ->orWhere('colegio', 'LIKE', "$search%")
            ->orWhere('numero_receta', 'LIKE', "$search%")
            ->whereBetween('fecha_carga', [$desde, $hasta])
            ->get();
    }

    public function findByListFarmaciaAndFechaAndUsario($search, $desde, $hasta, $usuario)
    {
        return RecetasModelo::with('Afiliado', 'Farmacia')
            ->where(function ($query) use ($search) {
                $query->whereHas('Afiliado', function ($queryAfiliado) use ($search) {
                    $queryAfiliado->where('nombre', 'LIKE', "$search%")->orWhere('dni', 'LIKE', "$search%");
                })->orWhereHas('Farmacia', function ($queryFarmacia) use ($search) {
                    $queryFarmacia->where('razon_social', 'LIKE', "$search%");
                });
            })
            ->orWhere('colegio', 'LIKE', "$search%")
            ->orWhere('numero_receta', 'LIKE', "$search%")
            ->whereBetween('fecha_carga', [$desde, $hasta])
            ->where('id_usuario', $usuario)
            ->get();
    }

    public function findByListIdUsuarioAndFechaBetweenAndLimit($iduser, $desde, $hasta)
    {
        return RecetasModelo::with('Afiliado', 'Farmacia')
            ->where('id_usuario', $iduser)
            ->whereBetween('fecha_carga', [$desde, $hasta])
            ->orderByDesc('fecha_carga')
            ->get();
    }

    public function findByListRecetariosAfiliado($dni)
    {
        return RecetasModelo::with('Afiliado', 'Farmacia')
            ->where(function ($query) use ($dni) {
                $query->whereHas('Afiliado', function ($queryAfiliado) use ($dni) {
                    $queryAfiliado->where('dni',   $dni);
                });
            })
            ->orderByDesc('fecha_carga')
            ->get();
    }
}
