<?php

namespace App\Models\SuperIntendencia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpedientesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_expedientes';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'rnos',
        'cuil_tit',
        'nombres',
        'cod_mov',
        'movimiento',
        'fecha_vigencia',
        'expediente',
        'año_expediente',
        'tipo_disposicion',
        'disposicion',
        'periodo',
        'id_usuario',
        'fecha_importacion',
    ];
}
