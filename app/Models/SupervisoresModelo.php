<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupervisoresModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_supervisores';
    protected $primaryKey = 'id_supervisor';
    public $timestamps = false;

    protected $fillable = [
        'nombres_supervisor',
        'activo'
    ];
}
