<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatalogiaModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_patalogia';
    protected $primaryKey = 'id_patalogia';
    public $timestamps = false;

    protected $fillable = [
        'patalogia'
    ];
}
