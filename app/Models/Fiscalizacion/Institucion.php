<?php

namespace App\Models\Fiscalizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Institucion extends Model
{
    use HasFactory;
    
    protected $table = 'tb_fisca_instituciones';
    protected $primaryKey = 'id_institucion';
    public $timestamps = false;

    protected $fillable = [
        'id_institucion',
        'descripcion',
        'vigente'
    ];
}