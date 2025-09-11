<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoBonoClinicoEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tipo_bono';
    protected $primaryKey = 'cod_tipo_bono';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
