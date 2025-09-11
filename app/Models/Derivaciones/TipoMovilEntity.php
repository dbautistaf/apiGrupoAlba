<?php

namespace  App\Models\Derivaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoMovilEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_derivacion_tipo_movil';
    protected $primaryKey = 'id_tipo_movil';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
