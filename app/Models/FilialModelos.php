<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilialModelos extends Model
{
    use HasFactory;
    protected $table = 'tb_filial';
    protected $primaryKey = 'id_filial';
    public $timestamps = false;

    protected $fillable = [
        'filial',
        'activo'
    ];
}
