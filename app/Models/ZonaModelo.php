<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZonaModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_zonas';
    protected $primaryKey = 'id_zona';
    public $timestamps = false;

    protected $fillable = [
        'zona'
    ];
}
