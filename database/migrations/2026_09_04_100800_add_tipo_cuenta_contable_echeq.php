<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Andamiaje contable del eCheq (punto 9), calcado del patrón que ya funciona en ospf
 * (ver `repo_ospf/ospf/docs/2026-08-05 - Tesoreria - Plan asientos automaticos cheques diferidos.md`).
 *
 * El problema: al emitir un eCheq la plata todavía NO salió del banco. El asiento tiene que ir
 * contra una cuenta puente de pasivo ("obligaciones a pagar por cheques diferidos"), y recién
 * cuando la conciliación confirma el débito se salda esa cuenta contra Banco.
 *
 * La cuenta puente se resuelve **por cuenta bancaria**, igual que la de banco: cada cuenta que
 * emite eCheq tiene su propia contrapartida diferida. Por eso se agrega un discriminador `tipo`
 * a `tb_cont_banco_cuenta_contable` en vez de crear una tabla nueva.
 *
 * ═══ Esta migración NO activa nada ═══
 *
 * Solo prepara el mecanismo. El asiento de emisión no se puede generar hasta que Contabilidad
 * cargue el mapeo `cuenta bancaria → cuenta contable de eCheq diferido`, y para eso hacen falta
 * cuentas que **hoy no existen en dos de los tres planes** (ALBA y Tripalium tienen solo
 * "cheques diferidos A COBRAR", que es la contrapartida del activo). Ver docs/plan-fase1-pagos.md §8.
 *
 * ═══ El bug latente que esto destapa ═══
 *
 * `obtenerCuentaContableByCuentaBancaria()` no filtra por tipo. Con una sola fila por cuenta
 * bancaria eso da igual; en cuanto exista la segunda (la diferida), `->first()` devolvería
 * cualquiera de las dos según el orden de inserción. ospf tropezó con exactamente esto. El
 * filtro explícito se agrega en el mismo cambio, ANTES de que exista ninguna fila nueva.
 */
return new class extends Migration
{
    public function up(): void
    {
        // El índice único falla si ya hay dos filas para la misma cuenta bancaria.
        $duplicados = DB::table('tb_cont_banco_cuenta_contable')
            ->select('id_cuenta_bancaria', DB::raw('COUNT(*) n'))
            ->groupBy('id_cuenta_bancaria')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        if ($duplicados > 0) {
            throw new \Exception(
                "Hay {$duplicados} cuenta(s) bancaria(s) con más de un mapeo contable. "
                . 'Resolverlos antes de crear el índice único.'
            );
        }

        if (!Schema::hasColumn('tb_cont_banco_cuenta_contable', 'tipo')) {
            DB::statement(
                "ALTER TABLE tb_cont_banco_cuenta_contable
                 ADD COLUMN tipo varchar(20) NOT NULL DEFAULT 'BANCO' AFTER id_detalle_plan"
            );

            // Todo lo que ya existe es la cuenta de banco/caja de siempre.
            DB::statement("UPDATE tb_cont_banco_cuenta_contable SET tipo = 'BANCO'");

            DB::statement(
                'ALTER TABLE tb_cont_banco_cuenta_contable
                 ADD UNIQUE KEY uq_banco_cuenta_tipo (id_cuenta_bancaria, tipo)'
            );
        }

        // Un asiento puede tener líneas de VARIOS pagos (dos eCheq de la misma OP comparten
        // asiento). Al anular uno solo hay que poder contraasentar su línea, no el asiento
        // entero — por eso el historial necesita apuntar al detalle, no solo a la cabecera.
        if (!Schema::hasColumn('tb_cont_asientos_pago_historial', 'id_asiento_contable_detalle')) {
            DB::statement(
                'ALTER TABLE tb_cont_asientos_pago_historial
                 ADD COLUMN id_asiento_contable_detalle int(11) NULL AFTER id_asiento_contable'
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tb_cont_banco_cuenta_contable', 'tipo')) {
            DB::statement('ALTER TABLE tb_cont_banco_cuenta_contable DROP INDEX uq_banco_cuenta_tipo');
            DB::statement('ALTER TABLE tb_cont_banco_cuenta_contable DROP COLUMN tipo');
        }

        if (Schema::hasColumn('tb_cont_asientos_pago_historial', 'id_asiento_contable_detalle')) {
            DB::statement('ALTER TABLE tb_cont_asientos_pago_historial DROP COLUMN id_asiento_contable_detalle');
        }
    }
};
