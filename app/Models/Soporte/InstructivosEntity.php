<?php

namespace   App\Models\Soporte;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructivosEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_instructivos';
    protected $primaryKey = 'id_instructivo';
    public $timestamps = false;

    protected $fillable = [
        'modulo',
        'descripcion',
        'nombre_archivo',
        'vigente'
    ];
}
