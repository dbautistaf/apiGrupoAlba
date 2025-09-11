<?php

namespace   App\Models\Internaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDiagnosticoInternacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_diagnostico_internacion';
    protected $primaryKey = 'cod_tipo_diagnostico';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente',
        'codigo_diagnostico',
        'id2',
        'id3'
    ];
}
