<?php

namespace App\Models\liquidaciones;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DictamenMedicoEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_liquidaciones_dictamen_medicos';
    protected $primaryKey = 'id_dictamen_medico';
    public $timestamps = false;

    protected $fillable = [
        'id_factura',
        'observacion_auditoria',
        'nombre_archivo',
        'cod_usuario',
        'fecha_registra',
        'fecha_actualiza'
    ];

    public function usuario()
    {
        return $this->hasOne(User::class, 'cod_usuario', 'cod_usuario');
    }
}
