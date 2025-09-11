<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModoEntrega extends Model
{
    use HasFactory;
    protected $table = 'tb_modo_entrega';
    protected $primaryKey = 'id_modo_entrega';
    public $timestamps = false;

    protected $fillable = [
        'detalle_entrega',
        'estado'
    ];
}
