<?php

namespace App\Models\medicacionAltoCosto;

use App\Models\afiliado\AfiliadoPadronEntity;
use App\Models\AutorizacionModels;
use App\Models\EstadoPago;
use App\Models\TipoAutorizacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicacionAltoCosto extends Model
{
    use HasFactory;
    protected $table = 'tb_medicacion_alto_costo';
    protected $primaryKey = 'id_medicacion_alto_costo';
    public $timestamps = false;

    protected $fillable = [
        'dni_afiliado',
        'id_tipo_autorizacion',
        'matricula_medico',
        'nombre_medico',
        'numero_tramite',
        'id_estado_tratamiento',
        'fecha_autorizacion',
        'id_estado_pago',
        'importe_cubierto',
        'importe_afiliado',
        'fecha_entrega',
        'id_modo_entrega',
        'responsable_entrega',
        'diagnostico',
        'indicaciones_medicas',
        'observaciones',
        'fecha_registro',
        'archivo',
        'id_usuario',
        'estado_registro'
    ];

    protected $appends = ['prestador_ganador', 'presupuesto_prestador_ganador'];

    public function detalle()
    {
        return $this->hasMany(MedicacionAltoCostoDetalle::class, 'id_medicacion_alto_costo');
    }

    public function comprobantes()
    {
        return $this->hasMany(DetalleComprobantesEntity::class, 'id_medicacion_alto_costo');
    }

    public function afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'dni', 'dni_afiliado');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoPago::class, 'id_estado_pago');
    }

    public function presupuesto()
    {
        return $this->hasMany(MedicacionAltoCostoPresupuesto::class, 'id_medicacion_alto_costo');
    }

    public function autorizacion()
    {
        return $this->belongsTo(TipoAutorizacion::class, 'id_tipo_autorizacion');
    }

    // Accesor para obtener el prestador ganador
    public function getPrestadorGanadorAttribute()
    {
        // Buscar el presupuesto donde 'gano_licitacion' sea 1
        $presupuestoGanador = $this->presupuesto->where('gano_licitacion', 1)->first();

        // Si se encuentra el presupuesto ganador y tiene un prestador asociado, retornarlo
        if ($presupuestoGanador && $presupuestoGanador->prestador) {
            return $presupuestoGanador->prestador; // Retorna el prestador asociado al presupuesto ganador
        }

        // Si no hay un prestador ganador, retorna null
        return null;
    }

    // Accesor para obtener presupuestos del prestador ganador
    public function getPresupuestoPrestadorGanadorAttribute()
    {
        // Buscar el presupuesto donde 'gano_licitacion' sea 1
        $presupuestoGanador = $this->presupuesto()->with('detalle')->where('gano_licitacion', 1)->first();

        // Si se encuentra el presupuesto ganador y tiene un prestador asociado, retornarlo
        if ($presupuestoGanador && $presupuestoGanador->prestador) {
            return $presupuestoGanador->detalle; // Retorna el prestador asociado al presupuesto ganador
        }

        // Si no hay un prestador ganador, retorna null
        return null;
    }
}
