<?php

namespace   App\Models\PrestacionesMedicas;

use App\Models\LocatorioModelos;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleTramitePrestacionMedicaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_prestacion_medicas_detalle_tramite';
    protected $primaryKey = 'id_detalle_tramite';
    public $timestamps = false;

    protected $fillable = [
        'id_locatorio',
        'cod_sindicato',
        'id_tipo_tramite',
        'id_tipo_prioridad'
    ];

    public function tramite()
    {
        return $this->hasOne(TipoTramiteAutorizacionesEntity::class, 'id_tipo_tramite', 'id_tipo_tramite');
    }
    public function prioridad()
    {
        return $this->hasOne(TipoPrioridadAutorizacionesEntity::class, 'id_tipo_prioridad', 'id_tipo_prioridad');
    }

    public function obrasocial()
    {
        return $this->hasOne(LocatorioModelos::class,  'id_locatorio', 'id_locatorio');
    }
}
