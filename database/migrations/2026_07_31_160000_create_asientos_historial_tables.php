<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Las 4 tablas de historial de asientos (factura/pago/reintegro/discapacidad) se crearon
     * a mano en distintos entornos sin migración — esto documenta el esquema real y permite
     * crear las que falten sin romper las que ya existen.
     */
    public function up()
    {
        $this->crearSiNoExiste('tb_cont_asientos_pago_historial', 'id_pago_asiento', 'id_pago');
        $this->crearSiNoExiste('tb_cont_asientos_reintegros_historial', 'id_reintegro_asiento', 'id_reintegro');
        $this->crearSiNoExiste('tb_cont_asientos_discapacidad_historial', 'id_discapacidad_asiento', 'id_discapacidad');
        $this->crearSiNoExiste('tb_cont_asientos_facturacion_historial', 'id_facturacion_asiento', 'id_factura');
    }

    private function crearSiNoExiste(string $tabla, string $primaryKey, string $columnaEntidad): void
    {
        if (Schema::hasTable($tabla)) {
            return;
        }

        Schema::create($tabla, function (Blueprint $table) use ($primaryKey, $columnaEntidad) {
            $table->increments($primaryKey);
            $table->integer($columnaEntidad);
            $table->integer('id_asiento_contable');
            $table->enum('tipo_evento', ['ALTA', 'MODIFICACION', 'ANULACION']);
            $table->boolean('es_contraasiento')->default(false);
            $table->integer('id_asiento_origen')->nullable();
            $table->text('observacion')->nullable();
            $table->integer('cod_usuario');
            $table->dateTime('fecha_registra')->useCurrent();
        });
    }

    public function down()
    {
        // No se dropean acá: podrían ser tablas que ya existían antes de esta migración.
    }
};
