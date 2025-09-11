<?php

namespace App\Models\Protesis;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramaEspecialProtesisEntity extends Model
{

    use HasFactory;
    protected $table = 'tb_protesis_programa_especial';
    protected $primaryKey = 'id_programa_especial';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
