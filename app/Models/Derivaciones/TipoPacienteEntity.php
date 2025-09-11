<?php

namespace  App\Models\Derivaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPacienteEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_derivacion_tipo_paciente';
    protected $primaryKey = 'id_tipo_paciente';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
