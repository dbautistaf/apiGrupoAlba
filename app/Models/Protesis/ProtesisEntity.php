<?php

namespace App\Models\Protesis;

use App\Models\afiliado\AfiliadoPadronEntity;
use App\Models\Internaciones\TipoDiagnosticoInternacionEntity;
use App\Models\LocatorioModelos;
use App\Models\medicos\MedicosEntity;
use App\Models\prestadores\PrestadorEntity;
use App\Models\ubigeo\UbigeoProvinciasEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProtesisEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_protesis';
    protected $primaryKey = 'id_protesis';
    public $timestamps = false;

    protected $fillable = [
        'fecha_emision',
        'id_tipo_autorizacion',
        'id_locatorio',
        'dni_afiliado',
        'edad_afiliado',
        'fecha_inicia_prestacion',
        'discapacidad',
        'cod_tipo_diagnostico',
        'diagnostico_detallado',
        'indicaciones',
        'cod_provincia',
        'cod_medico_solicitante',
        'cod_prestador',
        'cod_medico_efector',
        'via_atencion',
        'obs_impresion',
        'obs_interna',
        'cod_usuario_registra',
        'fecha_actualiza',
        'num_autorizacion',
        'fecha_cirugia',
        'id_condicion',
        'id_estado',
        'num_presupuesto',
        'num_factura',
        'importe_autorizado',
        'importe_total',
        'nombre_archivo'
    ];

    public function detalle()
    {
        return $this->hasMany(ProtesisDetalleEntity::class, 'id_protesis', 'id_protesis');
    }

    public function afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'dni', 'dni_afiliado');
    }

    public function estado()
    {
        return $this->hasOne(EstadoSolicitudProtesisEntity::class, 'id_estado', 'id_estado');
    }

    public function condicion()
    {
        return $this->hasOne(CondicionProtesisEntity::class, 'id_condicion', 'id_condicion');
    }

    public function tipo()
    {
        return $this->hasOne(ProtesisTipoAutorizacionEntity::class, 'id_tipo_autorizacion', 'id_tipo_autorizacion');
    }

    public function origen()
    {
        return $this->hasOne(LocatorioModelos::class, 'id_locatorio', 'id_locatorio');
    }

    public function diagnostico()
    {
        return $this->hasOne(TipoDiagnosticoInternacionEntity::class, 'cod_tipo_diagnostico', 'cod_tipo_diagnostico');
    }

    public function provincia()
    {
        return $this->hasOne(UbigeoProvinciasEntity::class, 'cod_provincia', 'cod_provincia');
    }

    public function medico()
    {
        return $this->hasOne(MedicosEntity::class, 'id_medico', 'cod_medico_solicitante');
    }

    public function prestador()
    {
        return $this->hasOne(PrestadorEntity::class, 'cod_prestador', 'cod_prestador');
    }

    public function efector()
    {
        return $this->hasOne(MedicosEntity::class, 'id_medico', 'cod_medico_efector');
    }
}
