<?php

namespace  App\Models\Derivaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoSectorEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_derivacion_tipo_sector';
    protected $primaryKey = 'id_tipo_sector';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
