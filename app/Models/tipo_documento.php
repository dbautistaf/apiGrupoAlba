<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tipo_documento extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_documentos';
    protected $primaryKey = 'id_tipo_documento';    
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'tipo_documento'
    ];
}
