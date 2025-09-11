<?php

namespace App\Models\Pmi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEmbarazoModel extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_embarazo';
    protected $primaryKey = 'id_tipo_embarazo';
    public $timestamps = false;

    protected $fillable = [
        'descripcion_tipo',
        'estado'
    ];
}
