<?php

namespace App\Models\Fiscalizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EmpresaModelo;
use App\Models\User;

class Cobranza extends Model
{
    use HasFactory;

    protected $table = 'tb_fisca_cobranzas';
    protected $primaryKey = 'id_cobranza';
    public $timestamps = false;

    protected $fillable = [
        'importe_sueldo',
        'aporte',
        'contribucion',
        'aporte_snr',
        'bonificacion',
        'cobro_neto',
        'cobro_total',
        'comision',
        'contribucion_extraordinaria',
        'fecha_creacion',
        'fecha_pago',
        'gasto_mora',
        'honorarios',
        'intereses_financiacion',
        'intereses_moratorios',
        'numero_cheque',
        'numero_cuotas',
        'numero_recibo',
        'numero_transferencia',
        'observacion',
        'plan_pago',
        'total_cuotas',
        'usuario',
        'id_banco_cobranza',
        'id_empresa',
        'id_expediente',
        'id_forma_pago',
        'periodos_impresos',
        'estado'
    ];

    public function empresa()
    {
        return $this->belongsTo(EmpresaModelo::class, 'id_empresa', 'id_empresa');
    }

    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'id_expediente', 'id_expediente');
    }

    public function bancosCobranza()
    {
        return $this->belongsTo(BancosCobranza::class, 'id_banco_cobranza', 'id_banco_cobranza');
    }
    public function FormasPago()
    {
        return $this->belongsTo(FormasPago::class, 'id_forma_pago', 'id_forma_pago');
    }

    public function archivos()
    {
        return $this->hasMany(ArchivoCobranza::class, 'id_cobranza');
    }
}
