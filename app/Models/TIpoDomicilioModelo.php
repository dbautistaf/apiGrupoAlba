<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TIpoDomicilioModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_domicilio';
    protected $primaryKey = 'id_tipo_domicilio';
    public $timestamps = false;

    protected $fillable = [
        'tipo'
    ];
}
