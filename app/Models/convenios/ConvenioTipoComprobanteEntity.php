<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConvenioTipoComprobanteEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_prestador_tipo_comprobantes';
    protected $primaryKey = 'id_tipo_comprobantes';
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
