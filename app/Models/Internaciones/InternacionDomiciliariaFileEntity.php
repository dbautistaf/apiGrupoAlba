<?php

namespace App\Models\Internaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternacionDomiciliariaFileEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_internaciones_domiciliaria_file';
    protected $primaryKey = 'id_internaciones_file';
    public $timestamps = false;

    protected $fillable = [
        'archivo',
        'fecha_carga',
        'id_internacion_domiciliaria'
    ];
}
