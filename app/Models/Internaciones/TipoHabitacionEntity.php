<?php

namespace   App\Models\Internaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoHabitacionEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tipo_habitacion';
    protected $primaryKey = 'cod_tipo_habitacion';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
