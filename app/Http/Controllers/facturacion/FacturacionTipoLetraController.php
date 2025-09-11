<?php

namespace App\Http\Controllers\facturacion;

use App\Http\Controllers\facturacion\repository\TipoLetraRepository;
use App\Models\facturacion\FacturacionTipoLetraEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FacturacionTipoLetraController extends Controller
{

    public function getListaTipoLetraFactura(Request $request, TipoLetraRepository $repository)
    {
        if (!is_null($request->vigente)) {
            $data = $repository->findByListTipoLetra();
            return response()->json($data);
        }
        return response()->json(FacturacionTipoLetraEntity::get());
    }

    public function getProcesarTipoLetraFactura(Request $request)
    {
        try {
            if (FacturacionTipoLetraEntity::where('tipo', $request->tipo)->exists()) {
                $tipo = FacturacionTipoLetraEntity::find($request->tipo);
                $tipo->update($request->all());
            } else {
                FacturacionTipoLetraEntity::create($request->all());
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
        return response()->json(['message' => 'Registro procesado correctamente.']);
    }

    public function getEliminarTipoLetraFactura(Request $request)
    {
        try {

            $tipo = FacturacionTipoLetraEntity::find($request->tipo);
            $tipo->delete();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
        return response()->json(['message' => 'Registro eliminado correctamente.']);
    }
}
