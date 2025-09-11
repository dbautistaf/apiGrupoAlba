<?php

namespace App\Models;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CredencialModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_credencial';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'num_carnet',
        'fecha_emision',
        'fecha_vencimiento',
        'id_padron'
    ];

    public function afiliado(){
        return $this->hasOne(AfiliadoPadronEntity::class,'id', 'id_padron');
    }
}
