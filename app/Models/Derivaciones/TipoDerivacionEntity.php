<?php

namespace  App\Models\Derivaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDerivacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_derivacion_tipo_derivacion';
    protected $primaryKey = 'id_tipo_derivacion';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
