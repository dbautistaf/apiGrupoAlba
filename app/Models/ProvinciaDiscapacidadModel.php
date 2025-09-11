<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvinciaDiscapacidadModel extends Model
{
    use HasFactory;
    protected $table = 'tb_provincia_discapacidad';
    protected $primaryKey = 'id_provincia_discapacidad';
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_provincia_discapacidad',
        'nombre'
    ];




}
