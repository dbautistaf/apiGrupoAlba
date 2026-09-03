<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Convierte el Pago en un instrumento con ciclo de vida propio (eCheq / transferencia).
     *
     * Se EXTIENDE tb_tes_pago en vez de crear una tabla nueva, y es deliberado: la contabilidad
     * vincula cada asiento con su pago por `id_pago` (tb_cont_asientos_pago_historial). Si los
     * pagos se recrearan en otra tabla, se perdería la capacidad de generar el contraasiento de
     * un pago viejo. "La contabilidad tiene que seguir activa" es restricción dura del
     * requerimiento.
     *
     * `numero_echeq` es columna NUEVA y no reusa `num_cheque` a propósito: la vieja tiene datos
     * sucios — de 9 registros en Alba, 2 con espacios y 4 con DOS números en el mismo campo
     * ("66439499/66439286"). Esos 4 son órdenes pagadas con dos cheques, que en el modelo nuevo
     * son dos Pagos separados. La columna vieja queda como histórico; la nueva nace limpia.
     *
     * El UNIQUE sobre numero_echeq implementa la regla "un eCheq cubre una única OP". MySQL
     * permite múltiples NULL en un índice único, así que las transferencias y los eCheq todavía
     * sin número no se estorban entre sí.
     *
     * NOTA: esto es lo OPUESTO a lo que hace ospf, que ante un número repetido acumula el monto
     * en el registro existente por diseño explícito. No copiar ese comportamiento.
     */
    public function up()
    {
        Schema::table('tb_tes_pago', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_tes_pago', 'id_estado_instrumento')) {
                $table->integer('id_estado_instrumento')->nullable()->after('id_estado_orden_pago');
            }
            if (!Schema::hasColumn('tb_tes_pago', 'numero_echeq')) {
                $table->string('numero_echeq', 50)->nullable()->after('num_cheque');
            }
            if (!Schema::hasColumn('tb_tes_pago', 'fecha_emision_echeq')) {
                $table->date('fecha_emision_echeq')->nullable()->after('numero_echeq');
            }
            if (!Schema::hasColumn('tb_tes_pago', 'id_banco_emisor')) {
                $table->integer('id_banco_emisor')->nullable()->after('fecha_emision_echeq');
            }
        });

        // Índice único aparte: si se agrega junto al ADD COLUMN y la columna ya existía,
        // el ALTER falla entero.
        $yaExiste = collect(Schema::getConnection()
            ->select("SHOW INDEX FROM tb_tes_pago WHERE Key_name = 'uq_numero_echeq'"))
            ->isNotEmpty();

        if (!$yaExiste) {
            Schema::table('tb_tes_pago', function (Blueprint $table) {
                $table->unique('numero_echeq', 'uq_numero_echeq');
            });
        }
    }

    public function down()
    {
        Schema::table('tb_tes_pago', function (Blueprint $table) {
            $table->dropUnique('uq_numero_echeq');
            $table->dropColumn([
                'id_estado_instrumento',
                'numero_echeq',
                'fecha_emision_echeq',
                'id_banco_emisor',
            ]);
        });
    }
};
