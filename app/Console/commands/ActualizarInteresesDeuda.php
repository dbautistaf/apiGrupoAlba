<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ActualizarInteresesDeuda extends Command
{
    protected $signature = 'deudas:actualizar-intereses';
    protected $description = 'Recalcula diariamente los intereses de las deudas impagas';

    public function handle()
    {
        $hoy = now()->toDateString();

        // Traer deudas vigentes
        $deudas = DB::table('tb_fisca_deudas_aportes_empresa')
            ->where('estado', 'Vigente')
            ->where('fecha_vencimiento', '>=', '2024-01-01') //por afip no tenemos tasas anteriores
            ->get();

        foreach ($deudas as $deuda) {
            // Buscar la(s) tasa(s) vigentes desde el vencimiento hasta hoy
            $tasas = DB::table('tb_fisca_tasas_interes')
                ->where('articulo_resolucion', 'Artículo 1°')
                ->where(function ($q) use ($deuda, $hoy) {
                    $q->where('vigencia_inicio', '<=', $hoy)
                        ->where(function ($q2) use ($deuda, $hoy) {
                            $q2->whereNull('vigencia_fin')
                                ->orWhere('vigencia_fin', '>=', $deuda->fecha_vencimiento);
                        });
                })
                ->orderBy('vigencia_inicio')
                ->get();


            $interesCalculado = 0;

            foreach ($tasas as $tasa) {
                // Calcular días aplicables de esta tasa
                $inicio = \Carbon\Carbon::parse($deuda->fecha_vencimiento)->greaterThan(\Carbon\Carbon::parse($tasa->vigencia_inicio))
                    ? \Carbon\Carbon::parse($deuda->fecha_vencimiento)
                    : \Carbon\Carbon::parse($tasa->vigencia_inicio);

                $fin = \Carbon\Carbon::parse($tasa->vigencia_fin ?? $hoy)->lessThan(\Carbon\Carbon::parse($hoy))
                    ? \Carbon\Carbon::parse($tasa->vigencia_fin)
                    : \Carbon\Carbon::parse($hoy);


                $dias = (new \Carbon\Carbon($inicio))->diffInDays(new \Carbon\Carbon($fin)) + 1;

                $interesCalculado += $deuda->monto_deuda * $tasa->interes_diario * $dias;
            }

            // Actualizar la deuda
            DB::table('tb_fisca_deudas_aportes_empresa')
                ->where('id_deuda', $deuda->id_deuda)
                ->update([
                    'intereses' => round($interesCalculado, 2),
                    'monto_deuda' => round($deuda->monto_deuda + $interesCalculado, 2)
                ]);
        }

        $this->info('Intereses actualizados correctamente al ' . $hoy);
    }
}
