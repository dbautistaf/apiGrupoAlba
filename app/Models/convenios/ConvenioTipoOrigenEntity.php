<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConvenioTipoOrigenEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_tipo_origen';
    protected $primaryKey = 'id_tipo_origen';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
