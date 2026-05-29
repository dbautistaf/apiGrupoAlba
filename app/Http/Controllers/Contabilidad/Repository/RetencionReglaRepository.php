<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\RetencionReglasEntity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class RetencionReglaRepository
{

    private $user;
    private $fechaActual;
    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now();
    }

    /**
     * Obtiene las reglas de retención vigentes para una fecha específica
     * 
     * @param string $fecha Fecha en formato Y-m-d
     * @param int|null $id_retencion ID del tipo de retención (opcional)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByReglasVigentesPorFecha($fecha, $id_retencion = null)
    {
        $query = RetencionReglasEntity::with(['tipoRetencion'])
            ->where('vigente', true)
            ->where('fecha_desde', '<=', $fecha)
            ->where(function ($q) use ($fecha) {
                $q->where('fecha_hasta', '>=', $fecha)
                    ->orWhereNull('fecha_hasta');
            });

        if ($id_retencion) {
            $query->where('id_retencion', $id_retencion);
        }

        return $query->orderBy('id_retencion')
            ->orderBy('fecha_desde', 'desc')
            ->get();
    }

    /**
     * Obtiene una regla específica vigente para un tipo de retención y fecha
     * 
     * @param int $id_retencion
     * @param string $fecha
     * @return RetencionReglasEntity|null
     */
    public function findRetencionVigentePorTipoYFecha($id_retencion, $fecha)
    {
        return RetencionReglasEntity::with(['tipoRetencion'])
            ->where('id_retencion', $id_retencion)
            ->where('vigente', 1)
            ->where('fecha_desde', '<=', $fecha)
            ->where(function ($q) use ($fecha) {
                $q->where('fecha_hasta', '>=', $fecha)
                    ->orWhereNull('fecha_hasta');
            })
            ->orderBy('fecha_desde', 'desc')
            ->first();
    }

}
