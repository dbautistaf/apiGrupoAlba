<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEmisionModel extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_emision';
    protected $primaryKey = 'id_tipo_emision';
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_emision',
        'tipo_emision',
        'vigente'
    ];
}
