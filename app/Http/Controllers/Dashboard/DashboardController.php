<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Dashboard\Repository\DashboardRepository;

class DashboardController extends Controller
{
    public function getDashboard(Request $request, DashboardRepository $repository)
    {
        // Validación de fechas
        $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date|after_or_equal:desde',
        ]);

        $desde = $request->get('desde');
        $hasta = $request->get('hasta');

        $data = $repository->getDashboardTotals($desde, $hasta);

        return response()->json([
            'success' => true,
            'data' => $data,
            'filtros' => [
                'desde' => $desde,
                'hasta' => $hasta,
            ]
        ]);
    }
}