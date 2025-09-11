<?php

namespace App\Models\Derivaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEstadoDerivacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_derivacion_tipo_estado';
    protected $primaryKey = 'id_tipo_estado';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente',
        'class_name'
    ];
}
