<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConvenioTipoPropuestaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_tipo_propuesta';
    protected $primaryKey = 'id_tipo_propuesta';
    public $timestamps = false;

    protected $fillable = [
        'tipo_propuesta',
        'activo'
    ];
}
