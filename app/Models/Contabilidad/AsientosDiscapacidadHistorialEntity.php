<?php

namespace App\Models\Contabilidad;

use App\Models\Discapacidad\IntegracionDiscapacidadEntity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AsientosDiscapacidadHistorialEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_cont_asientos_discapacidad_historial';
    protected $primaryKey = 'id_discapacidad_asiento';
    public $timestamps = false;

    protected $fillable = [
        'id_discapacidad',
        'id_asiento_contable',
        'tipo_evento',
        'es_contraasiento',
        'id_asiento_origen',
        'observacion',
        'cod_usuario',
        'fecha_registra'
    ];

    protected $casts = [
        'fecha_registra' => 'datetime',
        'es_contraasiento' => 'boolean'
    ];

    // Relación con la prestación de discapacidad
    public function discapacidad()
    {
        return $this->belongsTo(IntegracionDiscapacidadEntity::class, 'id_discapacidad', 'id_discapacidad');
    }

    // Relación con el asiento contable
    public function asientoContable()
    {
        return $this->belongsTo(AsientosContablesEntity::class, 'id_asiento_contable', 'id_asiento_contable');
    }

    // Relación con el asiento origen (para contraasientos)
    public function asientoOrigen()
    {
        return $this->belongsTo(AsientosContablesEntity::class, 'id_asiento_origen', 'id_asiento_contable');
    }
}