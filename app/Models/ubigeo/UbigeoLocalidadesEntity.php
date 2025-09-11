<?php

namespace App\Models\ubigeo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UbigeoLocalidadesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_ubigeo_localidades';
    protected $primaryKey = 'cod_localidad';
    public $timestamps = false;

    protected $fillable = [
        'nombre_localidad',
        'vigente',
        'cod_provincia'
    ];

    protected $hidden = [
        'pivot',
    ];

    public function provincia()
    {
        return $this->hasOne(UbigeoProvinciasEntity::class, 'cod_provincia', 'cod_provincia');
    }
}
