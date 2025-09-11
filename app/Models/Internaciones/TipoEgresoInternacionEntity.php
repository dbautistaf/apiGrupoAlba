<?php

namespace   App\Models\Internaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEgresoInternacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_egreso_internacion';
    protected $primaryKey = 'cod_tipo_egreso';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
