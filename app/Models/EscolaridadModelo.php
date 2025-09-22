<?php

namespace App\Models;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EscolaridadModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_escolaridad';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nivel_estudio',
        'fecha_presentacion',
        'fecha_vencimiento',
        'id_padron',
        'url_adjunto'
    ];
    public function afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'dni', 'id_padron');
    }
}
