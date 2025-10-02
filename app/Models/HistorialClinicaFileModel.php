<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialClinicaFileModel extends Model
{
    use HasFactory;
    protected $table = 'tb_historia_clinica_file';
    protected $primaryKey = 'id_file';
    public $timestamps = false;

    protected $fillable = [
        'url_file',
        'id_historia_clinica',
        'fecha_carga'
    ];
}
