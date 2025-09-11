<?php

namespace App\Models\Afiliado;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfiliadoCredencialEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_credencial';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'num_carnet',
        'fecha_emision',
        'fecha_vencimiento',
        'id_padron',
        'dni',
        'fecha_registra',
        'cod_usuario_registra',
        'fecha_modifica',
        'cod_usuario_modifica'
    ];

    public function afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'dni', 'dni');
    }
}
