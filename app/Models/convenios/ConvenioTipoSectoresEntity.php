<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConvenioTipoSectoresEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_sectores';
    protected $primaryKey = 'id_sector';
    public $timestamps = false;

    protected $fillable = [
        'sector',
        'activo'
    ];
}
