<?php

namespace   App\Models\Internaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoInternacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_internacion';
    protected $primaryKey = 'cod_tipo_internacion';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
