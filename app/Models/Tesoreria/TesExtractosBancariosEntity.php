<?php

namespace App\Models\Tesoreria;

use App\Models\LocatorioModelos;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesExtractosBancariosEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_extracto_bancarios';
    protected $primaryKey = 'id_extracto';
    public $timestamps = false;

    protected $fillable = [
        'id_entidad_bancaria',
        'fecha',
        'banco',
        'concepto',
        'importe',
        'saldo',
        'referencia',
        'detalle',
        // Campos Cygnus Finance AI
        'estado_conciliacion',
        'score_matching',
        'id_comprobante_financiero',
        // Campos de auditoría
        'id_usuario',
        'fecha_registra',
        'observaciones',
        'id_locatario'
    ];

    public function entidadBancaria()
    {
        return $this->hasOne(TesEntidadesBancariasEntity::class, 'id_entidad_bancaria', 'id_entidad_bancaria');
    }

    public function locatario()
    {
        return $this->hasOne(LocatorioModelos::class, 'id_locatorio', 'id_locatario');
    }
}
