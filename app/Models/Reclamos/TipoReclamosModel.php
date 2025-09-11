<?php

namespace App\Models\Reclamos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoReclamosModel extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_reclamos';
    protected $primaryKey = 'id_tipo_reclamo';
    public $timestamps = false;

    protected $fillable = [
        'reclamo',
        'activo'
    ];
}
