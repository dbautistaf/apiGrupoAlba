<?php

namespace App\Models\configuracion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gerenciadora extends Model
{
    use HasFactory;
    protected $table = 'tb_gerenciadora';
    protected $primaryKey = 'id_gerenciadora';
    public $timestamps = false;

    protected $fillable = [
        'detalle_gerenciadora',
        'estado',
    ];
}
