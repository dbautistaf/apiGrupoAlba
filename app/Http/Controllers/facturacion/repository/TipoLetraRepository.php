<?php

namespace App\Http\Controllers\facturacion\repository;
use App\Http\Controllers\facturacion\dto\TipoLetraDTO;
use App\Models\facturacion\FacturacionTipoLetraEntity;

class TipoLetraRepository
{

    public function findByListTipoLetra()
    {
        return FacturacionTipoLetraEntity::where('vigente', '1')
            ->get()
            ->map(function ($tipo) {
                return new TipoLetraDTO($tipo->tipo, $tipo->descripcion);
            });
    }

}
