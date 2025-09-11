<?php

namespace App\Models;

use App\Models\afiliado\AfiliadoTipoParentescoEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PadronComercialModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_padron_comercial';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
       
        'id_empresa',
        'cuil_tit',
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
        'id_tipo_domicilio',
        'email',
        'celular',
        'fe_baja',
        'activo',
        'id_cpostal',
        'observaciones',
        'id_tipo_carpeta',
        'id_qr',
        'id_supervisor',
        'id_agente',
        'id_regimen',
        'id_gerente',
        'id_gestoria',
        'aporte',
        'clave_fiscal',
        'tramite',
        'id_comercial_caja',
        'id_comercial_origen',
        'observaciones_auditoria',
        'id_estado_autorizacion',
        'id_locatario',
        'id_parentesco',
        'cuil_benef',
        'orden',
        'discapacidad',
        'rnos_anterior'
    ];

    public function Autorizacion(){
        return $this->hasOne(EstadoAutorizacionModelos::class,'id_estado_autorizacion', 'id_estado_autorizacion');
    }

    public function locatario(){
        return $this->hasOne(LocatorioModelos::class,'id_locatorio', 'id_locatario');
    }

    public function tipoParentesco()
    {
        return $this->hasOne(AfiliadoTipoParentescoEntity::class, 'id_parentesco','id_parentesco');
    }
    
    public function origen()
    {
        return $this->hasOne(ComercialOrigenModel::class, 'id_comercial_origen','id_comercial_origen');
    }

}
