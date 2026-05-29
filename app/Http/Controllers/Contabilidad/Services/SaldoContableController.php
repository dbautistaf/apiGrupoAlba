<?php
namespace App\Http\Controllers\Contabilidad\Services;

use App\Http\Controllers\Contabilidad\Repository\SaldoContableRepository;
use App\Http\Controllers\Contabilidad\Repository\PeriodosContablesRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class SaldoContableController extends Controller
{

    private $saldoContableRepository;
    private $periodoContableRepositorio;

    public function __construct(
        PeriodosContablesRepository $periodoContableRepositorio,
        SaldoContableRepository $saldoContableRepository
    ) {
        $this->saldoContableRepository = $saldoContableRepository;
        $this->periodoContableRepositorio = $periodoContableRepositorio;
    }

    public function getSaldosProveedor(Request $request)
    {
        $data = $this->saldoContableRepository->findByListarSaldos($request);
        return response()->json($data);
    }
}
