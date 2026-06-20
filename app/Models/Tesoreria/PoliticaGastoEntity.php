<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoliticaGastoEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_politica_gasto';
    protected $primaryKey = 'id_politica';
    public $timestamps = false;

    protected $fillable = [
        'concepto_clave',         // VARCHAR(100) (Ej: 'UBER', 'HONORARIOS')
        'monto_maximo_permitido', // DECIMAL(15,2) (Nullable si solo se bloquea por palabra)
        'estado_bloqueo',         // TINYINT(1) / BOOLEAN (1 = Bloqueado/Fuera de política, 0 = Permitido)
        'id_usuario_alta',        // BIGINT / INT
        'fecha_alta'              // DATETIME / TIMESTAMP
    ];
}
