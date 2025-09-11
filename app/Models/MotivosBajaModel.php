<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotivosBajaModel extends Model
{
    use HasFactory;
    protected $table = 'tb_bajas_motivos';
    protected $primaryKey = 'id_baja_motivos';
    public $timestamps = false;

    protected $fillable = [
        'motivo_baja'
    ];
}
