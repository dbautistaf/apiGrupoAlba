<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDocumentacionModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_documentacion';
    protected $primaryKey = 'cod_tipo_documentacion';
    public $timestamps = false;

    protected $fillable = [
        'tipo_documentacion'
    ];
}
