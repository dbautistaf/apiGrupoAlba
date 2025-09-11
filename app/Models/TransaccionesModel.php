<?php

namespace App\Models;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaccionesModel extends Model
{
    use HasFactory;
    protected $table = 'tb_transacciones_validador';
    protected $primaryKey = 'id_transacciones';
    public $timestamps = false;

    protected $fillable = [
        'id_autorizacion',
        'fecha_receta',
        'fecha_venta',
        'plan',
        'nro_receta',
        'cuil',
        'nombre_afiliado',
        'matricula_medico',
        'nombre_medico',
        'diagnostico',
        'nombre_farmacia',
        'cuit',
        'localidad',
        'fecha_carga',
        'id_usuario'
    ];
    public function afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'cuil_tit', 'cuil');
    }
    public function farmacia()
    {
        return $this->hasOne(FarmaciasModelo::class, 'cuit', 'cuit');
    }

    public function detalles()
    {
        return $this->hasMany(TransaccionesDetalleModel::class, 'id_transacciones');
    }
}
