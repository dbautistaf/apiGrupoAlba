<?php

namespace App\Models\Tesoreria;

use App\Models\configuracion\RazonSocialModelo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesCuentasBancariasEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_cuentas_bancarias';
    protected $primaryKey = 'id_cuenta_bancaria';
    public $timestamps = false;

    protected $fillable = [
        'id_razon',
        'numero_cuenta',
        'nombre_cuenta',
        'id_tipo_cuenta',
        'id_entidad_bancaria',
        'saldo_total',
        'saldo_disponible',
        'activo',
        'cbu',
        'alias',
        'fecha_apertura',
        'cod_usuario',
        'id_tipo_moneda',
        'limite_sobregiro'
    ];

    /**
     * Cuenta contable de BANCO/CAJA. Si viene null, esta cuenta bancaria NO se puede usar para
     * pagar: el asiento no tiene contra que imputar y la confirmacion del pago falla.
     */
    public function cuentaContableBanco()
    {
        return $this->hasOne(\App\Models\Contabilidad\BancoCuentasContableEntity::class, 'id_cuenta_bancaria', 'id_cuenta_bancaria')
            ->where('tipo', \App\Models\Contabilidad\BancoCuentasContableEntity::TIPO_BANCO)
            ->where('vigente', 1);
    }

    /**
     * Cuenta puente para los eCheq emitidos y todavia no debitados. Si viene null, esta cuenta
     * no esta habilitada para emitir eCheq.
     */
    public function cuentaContableEcheq()
    {
        return $this->hasOne(\App\Models\Contabilidad\BancoCuentasContableEntity::class, 'id_cuenta_bancaria', 'id_cuenta_bancaria')
            ->where('tipo', \App\Models\Contabilidad\BancoCuentasContableEntity::TIPO_ECHEQ_DIFERIDO)
            ->where('vigente', 1);
    }

    public function razonSocial()
    {
        return $this->hasOne(RazonSocialModelo::class, 'id_razon', 'id_razon');
    }

    public function tipoCuenta()
    {
        return $this->hasOne(TesTipoCuentasBancariasEntity::class, 'id_tipo_cuenta', 'id_tipo_cuenta');
    }

    public function entidadBancaria()
    {
        return $this->hasOne(TesEntidadesBancariasEntity::class, 'id_entidad_bancaria', 'id_entidad_bancaria');
    }

    public function tipoMoneda()
    {
        return $this->hasOne(TesTipoMonedasEntity::class, 'id_tipo_moneda', 'id_tipo_moneda');
    }
}
