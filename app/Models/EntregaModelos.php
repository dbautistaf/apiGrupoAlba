<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntregaModelos extends Model
{
    use HasFactory;
    protected $table = 'tb_entrega';
    protected $primaryKey = 'id_entrega';
    public $timestamps = false;

    protected $fillable = [
        'num_caja',
        'fecha_entrega',
        'observaciones',
        'id_usuario',
        'personal_recibe'
    ];
}
