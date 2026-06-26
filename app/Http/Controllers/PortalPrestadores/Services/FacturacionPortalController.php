<?php

namespace App\Http\Controllers\PortalPrestadores\Services;

use App\Http\Controllers\PortalPrestadores\Repository\DocumentacionRepository;
use App\Http\Controllers\PortalPrestadores\Repository\FacturacionPoralRepository;
use App\Http\Controllers\Utils\ManejadorDeArchivosUtils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FacturacionPortalController extends Controller
{
    private FacturacionPoralRepository $repo;
    private DocumentacionRepository $repoDoc;

    public function __construct(FacturacionPoralRepository $repo, DocumentacionRepository $repoDoc)
    {
        $this->repo = $repo;
        $this->repoDoc = $repoDoc;
    }


    public function listar()
    {
        return response()->json($this->repo->listar());
    }

    public function obtener($id)
    {
        return response()->json($this->repo->obtener($id));
    }

    public function crear(Request $request)
    {
        if (is_null($request->id_factura)) {
            if($this->repo->findByExistFactura($request)){
                return response()->json(['message' => 'La factura que intenta cargar ya éxiste, se solicita su verificación'],409);
            }
            return response()->json(['message' => 'Factura Registrada con éxito', 'data' => $this->repo->crear($request->all())]);
        } else {
            return response()->json(['message' => 'Factura modificada con éxito', 'data' => $this->repo->actualizar($request->id_factura, $request->all())]);
        }
    }

    public function actualizar(Request $request, $id)
    {
        return response()->json($this->repo->actualizar($id, $request->all()));
    }

    public function eliminar($id)
    {
        return response()->json($this->repo->eliminar($id, auth()->user()->id));
    }

    public function listarEstados()
    {
        return response()->json($this->repo->listarEstados());
    }

    public function actualizarEstado(Request $request)
    {
        return response()->json(['message' => 'Factura modificada con éxito', 'data' => $this->repo->actualizarEstado($request->id_factura, $request)]);
    }

    public function cargarDocumentacion(Request $request, ManejadorDeArchivosUtils $storage)
    {
        try {
            $prestador = Auth::user()->id_prestador;
            $storage->crearCarpeta('portal-prestadores', $prestador);
            $detalleArchivos = $storage->findByCargaMasivaArchivos('PORT_PREST', 'portal-prestadores/' . $prestador, $request);
            foreach ($detalleArchivos as $key) {
                $this->repoDoc->crear($request->id, $key['nombre']);
            }
            return response()->json(['message' => 'Documentacion cargada con éxito']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function getVerAdjunto(ManejadorDeArchivosUtils $storageFile, Request $request)
    {
        $prestador = Auth::user()->id_prestador;
        $path = "portal-prestadores/";
        $documento = $this->repoDoc->id($request->id);
        $anioTrabaja = Carbon::parse($documento->fecha_carga)->year;
        $path .= "{$prestador}/{$anioTrabaja}/$documento->documento";
        return $storageFile->findByObtenerArchivo($path);
    }

    public function listarDocumentacion(Request $request)
    {
        return response()->json($this->repoDoc->listar($request->id));
    }
}
