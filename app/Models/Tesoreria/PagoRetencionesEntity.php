<?php

namespace App\Models\Tesoreria;

use App\Models\User;
use App\Models\Contabilidad\TipoRetencionesEntity;
use App\Models\Contabilidad\RetencionReglasEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoRetencionesEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_pago_retencion';
    protected $primaryKey = 'id_pago_retencion';
    public $timestamps = false;

    protected $fillable = [
        'id_pago',
        'id_retencion',
        'id_retencion_regla',
        'base_imponible',
        'porcentaje',
        'monto',
        'minimo_aplicado',
        'observaciones',
        'fecha_registra',
        'id_usuario'
    ];

    protected $casts = [
        'base_imponible' => 'decimal:2',
        'porcentaje' => 'decimal:4',
        'monto' => 'decimal:2',
        'minimo_aplicado' => 'decimal:2',
        'fecha_registra' => 'datetime'
    ];

    public function pago()
    {
        return $this->belongsTo(TesPagoEntity::class, 'id_pago', 'id_pago');
    }

    public function tipoRetencion()
    {
        return $this->belongsTo(TipoRetencionesEntity::class, 'id_retencion', 'id_retencion');
    }

    public function regla()
    {
        return $this->belongsTo(RetencionReglasEntity::class, 'id_retencion_regla', 'id_regla');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'cod_usuario');
    }
}
