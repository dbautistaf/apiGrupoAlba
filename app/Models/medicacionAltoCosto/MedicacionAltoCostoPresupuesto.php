<?php

namespace App\Models\medicacionAltoCosto;

use App\Models\prestadores\PrestadorEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicacionAltoCostoPresupuesto extends Model
{
    use HasFactory;
    protected $table = 'tb_medicacion_alto_costo_presupuestos';
    protected $primaryKey = 'id_presupuesto';
    public $timestamps = false;

    protected $fillable = [
        'id_medicacion_alto_costo',
        'cod_prestador',
        'fecha_solicitud_presupuesto',
        'cod_usuario',
        'gano_licitacion',
        'fecha_registro_ganador',
        'cod_usuario_registra_ganador',
        'archivo_cotizacion'
    ];

    public function medicacion()
    {
        return $this->belongsTo(MedicacionAltoCosto::class, 'id_medicacion_alto_costo');
    }

    public function prestador()
    {
        return $this->belongsTo(PrestadorEntity::class, 'cod_prestador', 'cod_prestador');
    }

    public function detalle()
    {
        return $this->hasMany(MedicacionAltoCostoPresupuestoDetalle::class, 'id_presupuesto');
    }
}
