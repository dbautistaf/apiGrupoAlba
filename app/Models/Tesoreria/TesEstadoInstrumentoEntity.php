<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Catálogo de estados del INSTRUMENTO de pago (eCheq / transferencia).
 *
 * Es un catálogo distinto del de la OPA (`tb_tes_estado_orden_pago`): un eCheq puede estar
 * RECHAZADO sin que la OPA lo esté. La máquina de estados está documentada en
 * TesInstrumentoPagoRepository.
 */
class TesEstadoInstrumentoEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_estado_instrumento';
    protected $primaryKey = 'id_estado_instrumento';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_estado_instrumento',
        'descripcion_estado',
        'name_class',
        'vigente'
    ];
}
