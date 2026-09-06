<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Backfill de datos, no de esquema: no crea ni altera columnas.
 *
 * Confirmar una OPA (definir su cronograma) siempre dejó la orden en PENDIENTE(1) — nadie la
 * movía a EN_PROCESO(4), el estado del catálogo que existía justo para esto ("tiene boleta, no
 * está pagada todavía"). El código se corrigió (ver TesPagosRepository::findByCrearPago) para
 * que confirmar de ahora en más mueva la orden a EN_PROCESO; esta migración pone al día las
 * órdenes que ya habían sido confirmadas antes del fix y quedaron viéndose PENDIENTE con su
 * boleta ya creada — 69 órdenes al momento de escribir esto (2026-09-05).
 *
 * Sin este backfill, esas órdenes seguían mostrando el botón "Confirmar OPA" (ya corregido en el
 * front para no depender solo del estado, pero es la causa raíz real) y un reintento de
 * confirmarlas chocaba con "Esta orden de pago ya tiene un pago generado".
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('tb_tes_orden_pago as o')
            ->join('tb_tes_pago as p', 'p.id_orden_pago', '=', 'o.id_orden_pago')
            ->where('o.id_estado_orden_pago', 1)
            ->where('p.id_estado_orden_pago', '!=', 3)
            ->update(['o.id_estado_orden_pago' => 4]);
    }

    public function down(): void
    {
        // No se revierte: no hay forma de distinguir las que este backfill movió de las que ya
        // habían llegado a EN_PROCESO por otro camino antes de correrlo.
    }
};
