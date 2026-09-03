<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Alinea el catálogo de estados de OPA entre Alba y OSV, y agrega el que falta.
     *
     * Relevado el 2026-09-03: los catálogos DIVERGIERON entre las dos bases.
     *   - Alba tiene el estado 6 "PAGO PARCIAL"; OSV no lo tiene.
     *   - El name_class del estado 4 difiere (Alba "bg-info", OSV "bg-warning").
     *
     * Se agrega lo que falta con insertOrIgnore, sin pisar lo existente. NO se renumera nada:
     * los valores 1..5 conservan su significado. Renumerar para parecernos a otro sistema daría
     * vuelta el sentido de miles de registros históricos en silencio.
     *
     * El 7 CONSUMIDA es para los anticipos (fase 3): un anticipo pasa a consumido cuando su
     * saldo llega a cero entre todas sus aplicaciones.
     */
    public function up()
    {
        $estados = [
            [6, 'PAGO PARCIAL', 'badge bg-warning'],
            [7, 'CONSUMIDA',    'badge bg-secondary'],
        ];

        foreach ($estados as [$id, $desc, $clase]) {
            DB::table('tb_tes_estado_orden_pago')->insertOrIgnore([
                'id_estado_orden_pago' => $id,
                'descripcion_estado'   => $desc,
                'name_class'           => $clase,
                'vigente'              => 1,
            ]);
        }
    }

    public function down()
    {
        // Solo se borran si ninguna OPA los está usando.
        foreach ([6, 7] as $id) {
            $enUso = DB::table('tb_tes_orden_pago')
                ->where('id_estado_orden_pago', $id)->exists();

            if (!$enUso) {
                DB::table('tb_tes_estado_orden_pago')
                    ->where('id_estado_orden_pago', $id)->delete();
            }
        }
    }
};
