<?php

namespace App\Http\Controllers\SuperIntendencia\Services;

use App\Http\Controllers\SuperIntendencia\Repository\AdhesionAfipRepository;
use App\Http\Controllers\SuperIntendencia\Repository\AltasMonotributoRepsoitory;
use App\Http\Controllers\SuperIntendencia\Repository\AltasRegimenGeneralRepository;
use App\Http\Controllers\SuperIntendencia\Repository\BajaAutomaticaAfipRepository;
use App\Http\Controllers\SuperIntendencia\Repository\BajasMonotributoRepository;
use App\Http\Controllers\SuperIntendencia\Repository\BajasRegimenGeneralRepository;
use App\Http\Controllers\SuperIntendencia\Repository\DesempleoSuperIntendenciaRepository;
use App\Http\Controllers\SuperIntendencia\Repository\EfectoresSocialesRepository;
use App\Http\Controllers\SuperIntendencia\Repository\ExpedientesRepository;
use App\Http\Controllers\SuperIntendencia\Repository\FamiliaresMonotributoRepository;
use App\Http\Controllers\SuperIntendencia\Repository\SuperPadronRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SuperIntendenciaImportarPadronController extends Controller
{
    private AltasRegimenGeneralRepository $repoRegimenGeneral;
    private SuperPadronRepository $repoPadronSuper;
    private DesempleoSuperIntendenciaRepository $repoDesempleo;
    private AdhesionAfipRepository $repoAdhesion;
    private BajaAutomaticaAfipRepository $repoBajaAutomatica;
    private FamiliaresMonotributoRepository $repoFamiliaresMono;
    private EfectoresSocialesRepository $repoEfectosSociales;
    private BajasRegimenGeneralRepository $repoBajasGeneral;
    private ExpedientesRepository $repoExpediente;
    private BajasMonotributoRepository $repoBajaMono;
    private AltasMonotributoRepsoitory $repoAltasMono;
    public function __construct(AltasRegimenGeneralRepository $repoRegimenGeneral, SuperPadronRepository $repoPadronSuper, DesempleoSuperIntendenciaRepository $repoDesempleo, AdhesionAfipRepository $repoAdhesion, BajaAutomaticaAfipRepository $repoBajaAutomatica, FamiliaresMonotributoRepository $repoFamiliaresMono, EfectoresSocialesRepository $repoEfectosSociales, BajasRegimenGeneralRepository $repoBajasGeneral, ExpedientesRepository $repoExpediente, BajasMonotributoRepository $repoBajaMono, AltasMonotributoRepsoitory $repoAltasMono)
    {
        $this->repoRegimenGeneral = $repoRegimenGeneral;
        $this->repoPadronSuper = $repoPadronSuper;
        $this->repoDesempleo = $repoDesempleo;
        $this->repoAdhesion = $repoAdhesion;
        $this->repoBajaAutomatica = $repoBajaAutomatica;
        $this->repoFamiliaresMono = $repoFamiliaresMono;
        $this->repoEfectosSociales = $repoEfectosSociales;
        $this->repoBajasGeneral = $repoBajasGeneral;
        $this->repoExpediente = $repoExpediente;
        $this->repoBajaMono = $repoBajaMono;
        $this->repoAltasMono = $repoAltasMono;
    }

    public function getImportarPadron(Request $request)
    {

        try {
            set_time_limit(600);
            if ($request->hasFile('file')) {
                $archivo = $request->file('file');
                $lineas = explode("\n", $archivo->get());

                if ($request->tipoArchivo == '1') {
                    foreach ($lineas as $linea) {
                        $campos = explode('|', $linea);
                        if (isset($campos[1])) {
                            $existsRow =  $this->repoPadronSuper->findByExisteRow(trim($campos[6]), $request->periodo);
                            if (!$existsRow) {
                                $this->repoPadronSuper->findByCrearRow($campos, $request->periodo);
                            }
                        }
                    }
                } elseif ($request->tipoArchivo == '2') {
                    foreach ($lineas as $linea) {
                        $campos = explode('|', $linea);
                        if (isset($campos[1])) {
                            $existsRow = $this->repoDesempleo->findByExisteRow(trim($campos[4]), $request->periodo);
                            if (!$existsRow) {
                                $this->repoDesempleo->findByCrearRow($campos, $request->periodo);
                            }
                        }
                    }
                } elseif ($request->tipoArchivo == '3') {
                    foreach ($lineas as $linea) {
                        $campos = explode('|', $linea);
                        if (isset($campos[1])) {
                            $existsRow = $this->repoAdhesion->findByExisteRow(trim($campos[0]), $request->periodo);
                            if (!$existsRow) {
                                $this->repoAdhesion->findByCrearRow($campos, $request->periodo);
                            }
                        }
                    }
                } elseif ($request->tipoArchivo == '4') {
                    foreach ($lineas as $linea) {
                        $campos = explode('|', $linea);
                        if (isset($campos[1])) {
                            $existsRow = $this->repoBajaAutomatica->findByExisteRow(trim($campos[0]), $request->periodo);
                            if (!$existsRow) {
                                $this->repoBajaAutomatica->findByCrearRow($campos, $request->periodo);
                            }
                        }
                    }
                } elseif ($request->tipoArchivo == '5') {
                    foreach ($lineas as $linea) {
                        $campos = explode('|', $linea);
                        if (isset($campos[1])) {
                            $existsRow = $this->repoFamiliaresMono->findByExisteRow(trim($campos[3]), $request->periodo);
                            if (!$existsRow) {
                                $this->repoFamiliaresMono->findByCrearRow($campos, $request->periodo);
                            }
                        }
                    }
                } elseif ($request->tipoArchivo == '6') {
                    foreach ($lineas as $linea) {
                        $campos = explode('|', $linea);
                        if (isset($campos[1])) {
                            $existsRow = $this->repoEfectosSociales->findByExisteRow(trim($campos[0]), $request->periodo);
                            if (!$existsRow) {
                                $this->repoEfectosSociales->findByCrearRow($campos, $request->periodo);
                            }
                        }
                    }
                } elseif ($request->tipoArchivo == '7') {
                    foreach ($lineas as $linea) {
                        $campos = explode('|', $linea);
                        if (isset($campos[1])) {
                            $existsRow = $this->repoRegimenGeneral->findByExisteRow(trim($campos[0]), $request->periodo);
                            if (!$existsRow) {
                                $this->repoRegimenGeneral->findByCrearRow($campos, $request->periodo);
                            }
                        }
                    }
                } elseif ($request->tipoArchivo == '8') {
                    foreach ($lineas as $linea) {
                        $campos = explode('|', $linea);
                        if (isset($campos[1])) {
                            $existsRow = $this->repoBajasGeneral->findByExisteRow(trim($campos[0]), $request->periodo);
                            if (!$existsRow) {
                                $this->repoBajasGeneral->findByCrearRow($campos, $request->periodo);
                            }
                        }
                    }
                } elseif ($request->tipoArchivo == '9') {
                    foreach ($lineas as $linea) {
                        $campos = explode('|', $linea);
                        if (isset($campos[1])) {
                            $existsRow = $this->repoAltasMono->findByExisteRow(trim($campos[0]), $request->periodo);
                            if (!$existsRow) {
                                $this->repoAltasMono->findByCrearRow($campos, $request->periodo);
                            }
                        }
                    }
                } elseif ($request->tipoArchivo == '10') {
                    foreach ($lineas as $linea) {
                        $campos = explode('|', $linea);
                        if (isset($campos[1])) {
                            $existsRow = $this->repoBajaMono->findByExisteRow(trim($campos[0]), $request->periodo);
                            if (!$existsRow) {
                                $this->repoBajaMono->findByCrearRow($campos, $request->periodo);
                            }
                        }
                    }
                } elseif ($request->tipoArchivo == '11') {
                    foreach ($lineas as $linea) {
                        $campos = explode('|', $linea);
                        if (isset($campos[1])) {
                            $existsRow = $this->repoExpediente->findByExisteRow(trim($campos[1]), $request->periodo);
                            if (!$existsRow) {
                                $this->repoExpediente->findByCrearRow($campos, $request->periodo);
                            }
                        }
                    }
                }
                return response()->json(['message' => 'Archivo registrado correctamente'], 200);
            } else {
                return response()->json(["message" => "Se solicita un archivo .TXT para continuar."], 409);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
