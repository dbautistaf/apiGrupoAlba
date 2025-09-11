<?php

namespace App\Models\prestadores;

use App\Models\ubigeo\UbigeoProvinciasEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalPublicoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_prestadores_hospital_publico';
    protected $primaryKey = 'id_hospital';
    public $timestamps = false;

    protected $fillable = [
        'cuit',
        'nombre',
        'domicilio',
        'cod_provincia',
        'telefono',
        'fecha_alta',
    ];

    public function provincia()
    {
        return $this->hasOne(UbigeoProvinciasEntity::class, 'cod_provincia', 'cod_provincia');
    }
}
