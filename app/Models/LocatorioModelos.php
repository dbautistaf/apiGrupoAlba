<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocatorioModelos extends Model
{
    use HasFactory;
    protected $table = 'tb_locatorio';
    protected $primaryKey = 'id_locatorio';
    public $timestamps = false;

    protected $fillable = [
        'locatorio',
        'activo'
    ];

    protected $hidden = [
        'pivot',
    ];
}
