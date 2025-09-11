<?php

namespace   App\Models\Internaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPrestacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_prestacion';
    protected $primaryKey = 'cod_tipo_prestacion';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
