<?php

namespace App\Models\afiliado;

use App\Models\ComercialCajaModel;
use App\Models\PadronComercialModelo;
use App\Models\ComercialOrigenModel;
use App\Models\DetalleTipoDocAfiliadoModelo;
use App\Models\LocalidadModelo;
use App\Models\LocatorioModelos;
use App\Models\MotivosBajaModel;
use App\Models\TransaccionesModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfiliadoPadronEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_padron';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'cuil_tit',
        'cuil_benef',
        'id_tipo_documento',
        'dni',
        'nombre',
        'apellidos',
        'id_sexo',
        'id_estado_civil',
        'fe_nac',
        'id_nacionalidad',
        'calle',
        'numero',
        'piso',
        'depto',
        'id_localidad',
        'id_partido',
        'id_provincia',
        'telefono',
        'fe_alta',
        'id_usuario',
        'fecha_carga',
        'id_tipo_beneficiario',
        'id_situacion_de_revista',
        'id_tipo_domicilio',
        'id_parentesco',
        'email',
        'celular',
        'fe_baja',
        'activo',
        'id_estado_super',
        'id_cpostal',
        'observaciones',
        'id_delegacion',
        'domicilio_postal',
        'domicilio_laboral',
        'id_locatario',
        'patologia',
        'descripcion_patologia',
        'medicacion',
        'descripcion_medicacion',
        'credencial',
        'extracapita',
        'id_baja_motivos',
        'id_comercial_origen',
        'id_comercial_caja',
        'discapacidad',
    ];

    public function detalleplan()
    {
        return $this->hasMany(AfiliadoDetalleTipoPlanEntity::class, 'id_padron', 'dni');
    }


    public function certificado()
    {
        return $this->hasOne(AfiliadoCertificadoEntity::class, 'id_padron', 'id');
    }
    public function tipoParentesco()
    {
        return $this->hasOne(AfiliadoTipoParentescoEntity::class, 'id_parentesco', 'id_parentesco');
    }

    public function transaccion()
    {
        return $this->hasOne(TransaccionesModel::class,  'cuil', 'cuil_tit');
    }

    public function padroncomercial()
    {
        return $this->hasOne(PadronComercialModelo::class, 'dni', 'dni');
    }

    public function origen()
    {
        return $this->hasOne(ComercialOrigenModel::class, 'id_comercial_origen', 'id_comercial_origen');
    }

    public function caja()
    {
        return $this->hasOne(ComercialCajaModel::class, 'id_comercial_caja', 'id_comercial_caja');
    }

    public function obrasocial()
    {
        return $this->hasOne(LocatorioModelos::class,  'id_locatorio', 'id_locatario');
    }

    public function user()
    {
        return $this->hasOne(User::class,  'documento', 'dni');
    }

    public function localidad()
    {
        return $this->hasOne(LocalidadModelo::class,  'id_localidad', 'id_localidad');
    }

    public function documentos()
    {
        return $this->hasMany(DetalleTipoDocAfiliadoModelo::class, 'id_padron', 'id');
    }

    public function baja()
    {
        return $this->hasOne(MotivosBajaModel::class, 'id_baja_motivos', 'id_baja_motivos');
    }
}
