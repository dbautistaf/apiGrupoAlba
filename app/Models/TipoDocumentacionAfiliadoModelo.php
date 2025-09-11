<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDocumentacionAfiliadoModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_documentacion_afiliado';
    protected $primaryKey = 'id_tipo_documentacion';
    public $timestamps = false;

    protected $fillable = [
        'tipo_documentacion'
    ];
}
