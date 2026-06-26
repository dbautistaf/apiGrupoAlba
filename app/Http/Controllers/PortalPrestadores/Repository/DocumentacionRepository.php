<?php

namespace App\Http\Controllers\PortalPrestadores\Repository;

use App\Models\PortalPrestadores\DocumentacionFacturasEntity;
use Carbon\Carbon;

class DocumentacionRepository
{

    public function listar($idFactura)
    {
        return DocumentacionFacturasEntity::where('id_factura', $idFactura)->get();
    }

    public function crear($id, $archivo)
    {
        return DocumentacionFacturasEntity::create([
            'id_factura' => $id,
            'documento' => $archivo,
            'fecha_carga' => Carbon::now()
        ]);
    }

    public function eliminar($id)
    {
        return DocumentacionFacturasEntity::destroy($id);
    }

    public function id($id)
    {
        return DocumentacionFacturasEntity::find($id);
    }
}
