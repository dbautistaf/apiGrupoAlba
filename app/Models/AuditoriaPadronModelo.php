<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditoriaPadronModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_auditoria_padron';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'antes',
        'ahora',
        'id_padron',
        'cod_usuario'
    ];

    public function Usuario()
    {
        return $this->hasOne(User::class, 'cod_usuario', 'cod_usuario');
    }
}
