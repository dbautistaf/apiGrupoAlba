<?php

namespace App\Models\Contabilidad;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetencionReglasEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_cont_retencion_regla';
    protected $primaryKey = 'id_regla';
    public $timestamps = false;

    protected $fillable = [
        'id_retencion',
        'fecha_desde',
        'fecha_hasta',
        'porcentaje',
        'minimo_no_imponible',
        'base_calculo',
        'vigente',
        'fecha_registra',
        'id_usuario'
    ];

    protected $casts = [
        'fecha_desde' => 'date',
        'fecha_hasta' => 'date',
        'porcentaje' => 'decimal:4',
        'minimo_no_imponible' => 'decimal:2',
        'vigente' => 'boolean',
        'fecha_registra' => 'datetime'
    ];

    public function tipoRetencion()
    {
        return $this->belongsTo(TipoRetencionesEntity::class, 'id_retencion', 'id_retencion');
    }

}
