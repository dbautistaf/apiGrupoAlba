<?php

namespace App\Models\Fiscalizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BancosCobranza extends Model
{
    use HasFactory;
    
    protected $table = 'tb_fisca_bancos_cobranza';
    protected $primaryKey = 'id_banco_cobranza';
    public $timestamps = false;

    protected $fillable = ['descripcion_banco'];
}