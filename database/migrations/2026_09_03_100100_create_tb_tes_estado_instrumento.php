<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catálogo propio para el ciclo de vida del INSTRUMENTO de pago (eCheq / transferencia).
     *
     * Hoy tb_tes_pago no tiene estado propio: reusa id_estado_orden_pago, el catálogo de la OPA.
     * Esa conflación es la causa directa del bug reportado ("la orden queda bloqueada al guardar
     * la fecha de pago"): un pago recién creado, sin confirmar, se contaba como si ya hubiera
     * salido plata — bloqueando 1.020 órdenes en OSV y 69 en Alba sin motivo.
     *
     * El instrumento necesita sus propios estados porque su ciclo es distinto al de la orden:
     * el banco asigna el número del eCheq recién al emitirlo, y la acreditación la confirma la
     * conciliación bancaria días después.
     */
    public function up()
    {
        if (!Schema::hasTable('tb_tes_estado_instrumento')) {
            Schema::create('tb_tes_estado_instrumento', function (Blueprint $table) {
                $table->integer('id_estado_instrumento')->primary();
                $table->string('descripcion_estado', 100);
                $table->string('name_class', 50)->default('badge bg-secondary');
                $table->boolean('vigente')->default(1);
            });
        }

        $estados = [
            [1, 'BORRADOR',             'badge bg-secondary'],
            [2, 'PENDIENTE DE EMISION', 'badge bg-warning'],
            [3, 'EMITIDO',              'badge bg-info'],
            [4, 'ACREDITADO',           'badge bg-success'],
            [5, 'RECHAZADO',            'badge bg-danger'],
            [6, 'ANULADO',              'badge bg-dark'],
        ];

        foreach ($estados as [$id, $desc, $clase]) {
            DB::table('tb_tes_estado_instrumento')->insertOrIgnore([
                'id_estado_instrumento' => $id,
                'descripcion_estado'    => $desc,
                'name_class'            => $clase,
                'vigente'               => 1,
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('tb_tes_estado_instrumento');
    }
};
