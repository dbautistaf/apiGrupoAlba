<?php

namespace   App\Models\Soporte;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoportePrioridadModelo extends Model
{
    use HasFactory;
    protected $connection = 'mysql_soporte';
    protected $table = 'tb_prioridad';
    protected $primaryKey = 'id_prioridad';
    public $timestamps = false;

    protected $fillable = [
        'prioridad_tipo'
    ];
}
