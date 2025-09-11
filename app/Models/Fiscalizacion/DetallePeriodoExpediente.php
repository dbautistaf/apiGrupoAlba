<?php

namespace App\Models\Fiscalizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\PeriodoModelo as Periodo;


class DetallePeriodoExpediente extends Model
{
    protected $table = 'tb_fisca_detalle_periodos_expediente';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_deuda',
        'monto_deuda',
        'periodo',
        'id_expediente',
        'id_periodo',
        'fecha_modifica',
        'importe_sueldo',
        'aporte',
        'contribucion',
        'contribucion_extraordinaria',
        'intereses',
        'usuario_modifica',
    ];

    // Relación con expediente (opcional)
    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'id_expediente', 'id_expediente');
    }
    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'id_periodo', 'id_periodo');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_modifica', 'cod_usuario');
    }
}
