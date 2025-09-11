<?php

namespace App\Exports;

use App\Models\DiscaPacidadDetalleModel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SubsidiosExport implements FromView
{

    protected $params;

    public function __construct($param)
    {
        $this->params = $param;
    }
    public function view(): View
    {
        $dtDataExport = [];

        if (!empty($this->params['cuil_benf'])) {
            $search = $this->params['cuil_benf'];
            $dtDataExport = DiscaPacidadDetalleModel::with(['practica', 'subsidiodisca', 'disca', 'disca.afiliado'])
                ->whereHas('disca', function ($query) use ($search) {
                    $query->where('cuil_beneficiario', $search);
                })
                ->where('subsidio', 1)
                ->orderByDesc('id_discapacidad_detalle')
                ->get();
        } else if (!is_null($this->params->cuil_prestador)) {
            $search = $this->params->cuil_prestador;
            $dtDataExport = DiscaPacidadDetalleModel::with(['practica', 'subsidiodisca', 'disca', 'disca.afiliado'])
                ->whereHas('disca', function ($query) use ($search) {
                    $query->where('cuil_prestador', $search);
                })
                ->where('subsidio', 1)
                ->orderByDesc('id_discapacidad_detalle')
                ->get();
        } else if (!is_null($this->params->periodo)) {
            $search = $this->params->periodo;
            $dtDataExport = DiscaPacidadDetalleModel::with(['practica', 'subsidiodisca', 'disca', 'disca.afiliado'])
                ->whereHas('disca', function ($query) use ($search) {
                    $query->where('periodo_prestacion', $search);
                })
                ->where('subsidio', 1)
                ->orderByDesc('id_discapacidad_detalle')
                ->get();
        } else if (!is_null($this->params->factura)) {
            $search = $this->params->factura;
            $dtDataExport = DiscaPacidadDetalleModel::with(['practica', 'subsidiodisca', 'disca', 'disca.afiliado'])
                ->whereHas('disca', function ($query) use ($search) {
                    $query->where('num_factura', $search);
                })
                ->where('subsidio', 1)
                ->orderByDesc('id_discapacidad_detalle')
                ->get();
        } else {
            $dtDataExport = DiscaPacidadDetalleModel::with(['practica', 'subsidiodisca', 'disca', 'disca.afiliado', 'disca.prestador'])
                ->where('subsidio', 1)
                ->orderByDesc('id_discapacidad_detalle')
                ->get();
        }

        return view('exportsubsidio', [
            'dtsubsidio' => $dtDataExport
        ]);
    }
}
