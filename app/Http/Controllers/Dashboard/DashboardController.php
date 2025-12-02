<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Dashboard\Repository\DashboardRepository;

class DashboardController extends Controller
{
    public function getDashboard(Request $request, DashboardRepository $repository)
    {
        // Validación de fechas y marca
        $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date|after_or_equal:desde',
            'marca' => 'nullable|string' // Nuevo filtro
        ]);

        $desde = $request->get('desde');
        $hasta = $request->get('hasta');
        $marca = $request->get('marca'); // Nueva variable

        $data = $repository->getDashboardTotals($desde, $hasta, $marca);

        return response()->json([
            'success' => true,
            'data' => $data,
            'filtros' => [
                'desde' => $desde,
                'hasta' => $hasta,
                'marca' => $marca
            ]
        ]);
    }
}