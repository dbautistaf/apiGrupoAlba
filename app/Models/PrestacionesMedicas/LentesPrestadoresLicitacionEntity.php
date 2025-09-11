<?php

namespace  App\Models\PrestacionesMedicas;

use App\Models\prestadores\PrestadorEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LentesPrestadoresLicitacionEntity extends Model
{

    use HasFactory;
    protected $table = 'tb_lentes_solictar_presupuesto';
    protected $primaryKey = 'id_solicitud';
    public $timestamps = false;

    protected $fillable = [
        'id_solitud_lente',
        'cod_prestador',
        'fecha_solicita_presupuesto',
        'cod_usuario',
        'gano_licitacion',
        'fecha_registra_ganador',
        'cod_usuario_registra_ganador',
        'archivo_cotizacion',
        'monto_cotiza',
        'observaciones'
    ];

    public function prestador()
    {
        return $this->hasOne(PrestadorEntity::class, 'cod_prestador', 'cod_prestador');
    }
}
