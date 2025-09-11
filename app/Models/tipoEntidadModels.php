<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tipoEntidadModels extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_entidad';
    protected $primaryKey = 'id_tipo_entidad';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_emision',
        'tipo_entidad',
        'activo'
    ];
}
