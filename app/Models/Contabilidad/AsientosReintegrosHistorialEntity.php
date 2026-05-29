<?php

namespace App\Models\Contabilidad;

use App\Models\facturacion\FacturacionDatosEntity;
use App\Models\Reintegros\ReintegrosModelos;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsientosReintegrosHistorialEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_cont_asientos_reintegros_historial';
    protected $primaryKey = 'id_reintegro_asiento';
    public $timestamps = false;

    protected $fillable = [
        'id_reintegro',
        'id_asiento_contable',
        'tipo_evento',
        'es_contraasiento',
        'id_asiento_origen',
        'observacion',
        'cod_usuario',
        'fecha_registra'
    ];

    protected $casts = [
        'es_contraasiento' => 'boolean',
        'fecha_registra' => 'datetime'
    ];

    // Relación con la reintegro
    public function reintegro()
    {
        return $this->belongsTo(ReintegrosModelos::class, 'id_reintegro', 'id_reintegro');
    }

    // Relación con el asiento contable
    public function asientoContable()
    {
        return $this->belongsTo(AsientosContablesEntity::class, 'id_asiento_contable', 'id_asiento_contable');
    }

    // Relación con el asiento origen (cuando es contraasiento)
    public function asientoOrigen()
    {
        return $this->belongsTo(AsientosContablesEntity::class, 'id_asiento_origen', 'id_asiento_contable');
    }

    // Scopes para filtrar por tipo de evento
    public function scopeAlta($query)
    {
        return $query->where('tipo_evento', 'ALTA');
    }

    public function scopeModificacion($query)
    {
        return $query->where('tipo_evento', 'MODIFICACION');
    }

    public function scopeAnulacion($query)
    {
        return $query->where('tipo_evento', 'ANULACION');
    }

    // Scope para contraasientos
    public function scopeContraasientos($query)
    {
        return $query->where('es_contraasiento', true);
    }
}
