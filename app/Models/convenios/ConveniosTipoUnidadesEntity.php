<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConveniosTipoUnidadesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_tipo_valores_unidad';
    protected $primaryKey = 'cod_tipo_unidad';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
