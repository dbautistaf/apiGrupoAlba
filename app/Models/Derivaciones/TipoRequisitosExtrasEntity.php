<?php

namespace App\Models\Derivaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoRequisitosExtrasEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_derivacion_tipo_requisitos_extras';
    protected $primaryKey = 'id_tipo_requisito';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
