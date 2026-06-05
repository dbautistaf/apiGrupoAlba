<?php

namespace App\Models\Internaciones;

use App\Models\pratricaMatriz\PracticaMatrizEntity;
use App\Models\PrestacionesPracticaLaboratorioEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutorizacionDetalleRNEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_prestaciones_medicas_detalle_rn';
    protected $primaryKey = 'cod_detalle_rn';
    public $timestamps = false;

    protected $fillable = [
        'cantidad_solicitada',
        'cantidad_autorizada',
        'precio_unitario',
        'monto_pagar',
        'id_identificador_practica',
        'cod_prestacion_rn',
        'estado_imprimir'
    ];

    public function prestacion()
    {
        return $this->hasOne(PrestacionesPracticaLaboratorioEntity::class, 'cod_prestacion', 'cod_prestacion_rn');
    }

    public function practica()
    {
        return $this->hasOne(PracticaMatrizEntity::class, 'id_identificador_practica', 'id_identificador_practica');
    }
}
