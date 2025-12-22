<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsientosContablesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_cont_asientos_contables';
    protected $primaryKey = 'id_asiento_contable';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_asiento',
        'fecha_asiento',
        'asiento_modelo',
        'asiento_leyenda',
        'numero',
        'numero_referencia',
        'asiento_observaciones',
        'id_periodo_contable',
        'cod_usuario_crea',
        'fecha_registra',
        'cod_usuario_modifica',
        'fecha_modifica',
        'vigente'
    ];

    protected $casts = [
        'fecha_asiento' => 'date',
        'fecha_registra' => 'datetime',
        'fecha_modifica' => 'datetime',
        'numero' => 'integer',
        'numero_referencia' => 'integer'
    ];

    public function tipo()
    {
        return $this->hasOne(TipoAsientoContableEntity::class, 'id_tipo_asiento', 'id_tipo_asiento');
    }

    public function detalle()
    {
        return $this->hasMany(DetalleAsientosContablesEntity::class, 'id_asiento_contable', 'id_asiento_contable');
    }

    public function periodoContable()
    {
        return $this->hasOne(PeriodosContablesEntity::class, 'id_periodo_contable', 'id_periodo_contable');
    }
}
