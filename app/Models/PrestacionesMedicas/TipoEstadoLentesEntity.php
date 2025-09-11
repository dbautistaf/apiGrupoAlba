<?php
namespace App\Models\PrestacionesMedicas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEstadoLentesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_lentes_tipo_estado';
    protected $primaryKey = 'id_tipo_estado';
    public $timestamps = false;

    protected $fillable = [
        'estado',
        'class_name',
        'icon_badge'
    ];
}
