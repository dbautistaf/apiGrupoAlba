<?php

namespace App\Models\Diabetes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDiabetesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_padron_tipos_diabetes';
    protected $primaryKey = 'id_tipo_diabetes';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
