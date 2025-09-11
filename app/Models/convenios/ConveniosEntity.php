<?php

namespace App\Models\convenios;

use App\Models\configuracion\TipoPlanGalenosEntity;
use App\Models\LocatorioModelos;
use App\Models\pratricaMatriz\PracticaTipoCoberturaEntity;
use App\Models\ubigeo\UbigeoProvinciasEntity;
use App\Models\ubigeo\UbigeoLocalidadesEntity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConveniosEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios';
    protected $primaryKey = 'cod_convenio';
    public $timestamps = false;

    protected $fillable = [
        'descripcion_convenio',
        'fecha_inicio',
        'fecha_fin',
        'cod_provincia',
        'vigente',
        'cod_usuario_registra',
        'fecha_registra',
        'posee_coseguro'
    ];


    public function usuario()
    {
        return $this->hasOne(User::class, 'cod_usuario', 'cod_usuario_registra');
    }

    public function provincia()
    {
        return $this->hasOne(UbigeoProvinciasEntity::class, 'cod_provincia', 'cod_provincia');
    }

    public function categoriaPagos()
    {
        return $this->belongsToMany(ConveniosCategoriaPagosEntity::class, 'tb_convenios_pagos','cod_convenio','id_categoria_pago');
    }

    public function tipoValorizacion()
    {
        return $this->belongsToMany(ConveniosTipoValorizacionEntity::class, 'tb_convenios_valorizacion','cod_convenio','id_tipo_valotizacion');
    }

    public function altasCategorias()
    {
        return $this->belongsToMany(ConveniosAltaCategoriaEntity::class, 'tb_convenios_categorias','cod_convenio','id_alta_categoria');
    }

    public function tipoPlanes()
    {
        return $this->belongsToMany(TipoPlanGalenosEntity::class, 'tb_convenios_planes','cod_convenio','id_conf_galeno_plan');
    }

    public function localidades()
    {
        return $this->belongsToMany(UbigeoLocalidadesEntity::class, 'tb_convenios_localidades','cod_convenio','cod_localidad');
    }

    public function tipoCoberturas()
    {
        return $this->belongsToMany(PracticaTipoCoberturaEntity::class, 'tb_convenios_tipo_coberturas','cod_convenio','id_tipo');
    }

    public function locatarios()
    {
        return $this->belongsToMany(LocatorioModelos::class,'tb_convenios_locatarios', 'cod_convenio', 'id_locatorio');
    }

    public function origen()
    {
        return $this->belongsToMany(ConvenioTipoOrigenEntity::class,'tb_convenios_origen', 'cod_convenio', 'id_tipo_origen');
    }
}
