<?php

namespace App\Models\Protesis;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCoberturaProtesisEntity extends Model
{

    use HasFactory;
    protected $table = 'tb_protesis_tipo_cobertura';
    protected $primaryKey = 'id_tipo_cobertura';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
