<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentesModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_agentes';
    protected $primaryKey = 'id_agente';
    public $timestamps = false;

    protected $fillable = [
        'nombres_agente',
        'activo'
    ];
}
