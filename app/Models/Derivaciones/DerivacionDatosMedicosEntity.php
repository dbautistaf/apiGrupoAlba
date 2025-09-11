<?php

namespace App\Models\Derivaciones;

use App\Models\ubigeo\UbigeoLocalidadesEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DerivacionDatosMedicosEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_derivacion_datos_medicos';
    protected $primaryKey = 'id_derivacion_medico';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_traslado',
        'id_tipo_movil',
        'solicita_por',
        'medico_solicita',
        'entidad_solicitante',
        'telefono',
        'desde_institucion',
        'desde_telefono',
        'desde_domicilio',
        'desde_localidad',
        'hasta_institucion',
        'hasta_telefono',
        'hasta_domicilio',
        'hasta_localidad',
        'con_regreso',
        'con_espera',
        'num_internacion',
        'id_tipo_requisito',
        'cant_req_extra',
        'obs_req_extra'
    ];

    public function traslado()
    {
        return $this->hasOne(TipoMotivoTrasladoEntity::class, 'id_tipo_traslado', 'id_tipo_traslado');
    }

    public function movil()
    {
        return $this->hasOne(TipoMovilEntity::class, 'id_tipo_movil', 'id_tipo_movil');
    }

    public function dlocalidad()
    {
        return $this->hasOne(UbigeoLocalidadesEntity::class, 'cod_localidad', 'desde_localidad');
    }

    public function hlocalidad()
    {
        return $this->hasOne(UbigeoLocalidadesEntity::class, 'cod_localidad', 'hasta_localidad');
    }
}
