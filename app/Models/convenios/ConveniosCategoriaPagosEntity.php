<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConveniosCategoriaPagosEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_categoria_pagos';
    protected $primaryKey = 'id_categoria_pago';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];

    protected $hidden = [
        'pivot',
    ];
}
