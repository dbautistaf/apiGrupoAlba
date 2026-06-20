<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_tes_extracto_bancarios', function (Blueprint $table) {
            // Eliminar columnas que ya no se usan del esquema viejo
            $table->dropColumn([
                'fecha_operacion',
                'fecha_valor',
                'codigo',
                'num_cheque',
                'oficina',
                'monto_credito',
                'monto_debito',
                'monto_saldo_parcial',
                'monto_saldo_disponible',
                'num_documento',
                'causal'
            ]);

            // Agregar las columnas nuevas que pide el Excel y Cygnus AI
            $table->date('fecha')->nullable()->after('id_entidad_bancaria');
            $table->decimal('saldo', 18, 2)->nullable()->after('importe');
            $table->string('referencia', 100)->nullable()->after('saldo');
            
            // Campos de Inteligencia y Conciliación
            $table->string('estado_conciliacion', 50)->default('PENDIENTE')->after('detalle');
            $table->integer('score_matching')->nullable()->after('estado_conciliacion');
            $table->integer('id_comprobante_financiero')->nullable()->after('score_matching');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_tes_extracto_bancarios', function (Blueprint $table) {
            // Revertir agregados
            $table->dropColumn([
                'fecha',
                'saldo',
                'referencia',
                'estado_conciliacion',
                'score_matching',
                'id_comprobante_financiero'
            ]);

            // Restaurar viejas (tipos genéricos para rollback)
            $table->date('fecha_operacion')->nullable();
            $table->date('fecha_valor')->nullable();
            $table->string('codigo')->nullable();
            $table->string('num_cheque')->nullable();
            $table->string('oficina')->nullable();
            $table->decimal('monto_credito', 18, 2)->nullable();
            $table->decimal('monto_debito', 18, 2)->nullable();
            $table->decimal('monto_saldo_parcial', 18, 2)->nullable();
            $table->decimal('monto_saldo_disponible', 18, 2)->nullable();
            $table->string('num_documento')->nullable();
            $table->string('causal')->nullable();
        });
    }
};
