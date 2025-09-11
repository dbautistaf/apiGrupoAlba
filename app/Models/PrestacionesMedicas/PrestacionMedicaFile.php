<?php

namespace App\Models\PrestacionesMedicas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestacionMedicaFile extends Model
{
    use HasFactory;
    protected $table = 'tb_prestacion_medica_file';
    protected $primaryKey = 'id_prestacion_file';
    public $timestamps = false;

    protected $fillable = [
        'archivo',
        'fecha_carga',
        'cod_prestacion'
    ];
}
