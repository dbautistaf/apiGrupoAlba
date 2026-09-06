<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesPagosParciales extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_pago_parcial';
    protected $primaryKey = 'id_pago_parcial';
    public $timestamps = false;

    protected $fillable = [
        'fecha_registra',
        'fecha_confirma_pago',
        'id_forma_pago',
        'monto_pago',
        'id_usuario',
        'monto_opa',
        'num_cheque',
        'id_pago',
        // Vinculo con la fecha del cronograma que planifico este pago. Ver 2026_09_04_101000.
        'id_fecha_probable',
        'monto_restante',
        // Ciclo de vida del instrumento (eCheq). Cada abono ES un cheque: por eso estas
        // columnas viven aca y no en la boleta. Ver 2026_09_04_100900. Sin el fillable,
        // create() las descarta en silencio -- ya paso dos veces en esta fase.
        'id_estado_instrumento',
        'numero_echeq',
        'fecha_emision_echeq',
        'id_banco_emisor',
        // Cuenta de origen del pago. Vivia en la boleta (una sola para toda la orden), lo que
        // impedia pagar una misma orden desde dos bancos distintos. Ver 2026_09_06_100000.
        'id_cuenta_bancaria',
        'motivo_rechazo',
        'fecha_rechazo',
    ];

    public function formaPago()
    {
        return $this->hasOne(TesTipoFormasPagoEntity::class, 'id_forma_pago', 'id_forma_pago');
    }

    /** La boleta de pago a la que pertenece este abono. */
    public function pago()
    {
        return $this->hasOne(TesPagoEntity::class, 'id_pago', 'id_pago');
    }

    public function estadoInstrumento()
    {
        return $this->hasOne(TesEstadoInstrumentoEntity::class, 'id_estado_instrumento', 'id_estado_instrumento');
    }

    public function bancoEmisor()
    {
        return $this->hasOne(TesEntidadesBancariasEntity::class, 'id_entidad_bancaria', 'id_banco_emisor');
    }

    /** Cuenta de origen de ESTE pago — no la de la boleta. Ver 2026_09_06_100000. */
    public function cuentaBancaria()
    {
        return $this->hasOne(TesCuentasBancariasEntity::class, 'id_cuenta_bancaria', 'id_cuenta_bancaria');
    }
}
