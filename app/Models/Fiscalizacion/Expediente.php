<?php

namespace App\Models\Fiscalizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EmpresaModelo;
use App\Models\User;


class Expediente extends Model
{
    use HasFactory;

    protected $table = 'tb_fisca_expedientes';
    protected $primaryKey = 'id_expediente';
    public $timestamps = false;

    protected $fillable = [
        'id_empresa',
        'id_usuario',
        'numero_expediente',
        'fecha_creacion',
        'tipo_cuenta',
        'estado'
    ];

    // Relaciones
    public function empresa()
    {
        return $this->belongsTo(EmpresaModelo::class, 'id_empresa', 'id_empresa');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'cod_usuario');
    }

    public function periodos()
    {
        return $this->hasMany(DetallePeriodoExpediente::class, 'id_expediente', 'id_expediente');
    }

    public function historial()
    {
        return $this->hasMany(HistorialExpediente::class, 'id_expediente');
    }
}
