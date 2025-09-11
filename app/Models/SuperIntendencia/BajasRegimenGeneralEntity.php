<?php

namespace App\Models\SuperIntendencia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BajasRegimenGeneralEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_bajas_regimen_general';
    protected $primaryKey = 'id_baja_regimen_general';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'formulario',
        'rnos',
        'cuil_titular',
        'nombres',
        'telefono1',
        'telefono2',
        'calle',
        'altura',
        'piso',
        'departamento',
        'codigo_postal',
        'obra_social_elegida',
        'localidad',
        'provincia',
        'cuit_empresa',
        'empresa',
        'rnos_destino',
        'fecha_vigencia',
        'periodo',
        'fecha_importacion',
        'email',
        'cod_usuario_registra',
        'fecha_registra'
    ];
}
