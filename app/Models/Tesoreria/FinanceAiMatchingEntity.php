<?php

namespace App\Models\Tesoreria;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceAiMatchingEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_finance_ai_matching';
    protected $primaryKey = 'id_matching';
    public $timestamps = false;

    protected $fillable = [
        'id_extracto_bancario', // BIGINT / INT
        'tipo_origen_interno',  // VARCHAR(50) Ej: 'MOVIMIENTO_TESORERIA', 'ORDEN_PAGO'
        'id_origen_interno',    // BIGINT / INT
        'score_obtenido',       // DECIMAL(5,2) / FLOAT / INT (Para porcentaje del 0 al 100)
        'reglas_cumplidas',     // JSON / TEXT (Para guardar arrays stringificados)
        'id_usuario_aprobador', // BIGINT / INT (Nullable)
        'fecha_matching',       // DATETIME / TIMESTAMP
        'observaciones'         // TEXT / VARCHAR(255) (Nullable)
    ];

    protected $casts = [
        'reglas_cumplidas' => 'array',
        'fecha_matching' => 'datetime'
    ];

    public function extracto()
    {
        return $this->belongsTo(TesExtractosBancariosEntity::class, 'id_extracto_bancario', 'id_extracto');
    }

    public function usuarioAprobador()
    {
        return $this->belongsTo(User::class, 'id_usuario_aprobador', 'cod_usuario');
    }
}
