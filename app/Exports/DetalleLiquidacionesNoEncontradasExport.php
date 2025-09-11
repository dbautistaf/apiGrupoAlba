<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DetalleLiquidacionesNoEncontradasExport implements FromView
{

    protected $detalle;

    public function __construct($detalle)
    {
        $this->detalle = $detalle;
    }

    /**
     * Genera la vista para el archivo Excel
     */
    public function view(): View
    {
        return view('exports.detalle_liquidaciones_no_encontradas', [
            'detalle' => $this->detalle
        ]);
    }
}
