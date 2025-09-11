<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConveniosTipoCBUEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_tipo_cbu';
    protected $primaryKey = 'id_tipo_cbu';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
