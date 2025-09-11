<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodoModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_periodo';
    protected $primaryKey = 'id_periodo';
    public $timestamps = false;

    protected $fillable = [
        'periodo'
    ];
}
