<?php

namespace  App\Models\Derivaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEgresoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_derivacion_tipo_egreso';
    protected $primaryKey = 'id_tipo_egreso';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
