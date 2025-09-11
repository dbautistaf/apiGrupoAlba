<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConveniosTipoValorizacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_tipo_valorizacion';
    protected $primaryKey = 'id_tipo_valotizacion';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];

    protected $hidden = [
        'pivot',
    ];
}
