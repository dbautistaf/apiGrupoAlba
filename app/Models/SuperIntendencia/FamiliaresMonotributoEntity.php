<?php

namespace  App\Models\SuperIntendencia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamiliaresMonotributoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_familiares_monotributo';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'obra_social',
        'cuit_titular',
        'tipo_documento_fam',
        'nro_documento_fam',
        'apellido_fam',
        'nombres_fam',
        'parentesco_fam',
        'fecha_alta_fam',
        'id_usuario',
        'periodo_importacion',
        'fecha_importacion'
    ];
}
