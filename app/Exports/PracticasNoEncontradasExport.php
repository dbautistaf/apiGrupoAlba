<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PracticasNoEncontradasExport implements FromView
{

    protected $practicas;

    public function __construct($practicas)
    {
        $this->practicas = $practicas;
    }

    /**
     * Genera la vista para el archivo Excel
     */
    public function view(): View
    {
        return view('exports.practicas_no_encontradas', [
            'practicas' => $this->practicas
        ]);
    }
}
