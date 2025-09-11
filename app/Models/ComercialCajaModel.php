<?php

namespace App\Models;

use App\Models\configuracion\Gerenciadora;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComercialCajaModel extends Model
{
    use HasFactory;
    protected $table = 'tb_comercial_caja';
    protected $primaryKey = 'id_comercial_caja';
    public $timestamps = false;

    protected $fillable = [
        'nros',
        'detalle_comercial_caja',
        'id_locatario',
        'id_gerenciadora',
        'activo'
    ];

    public function locatario(){
        return $this->hasOne(LocatorioModelos::class,'id_locatorio', 'id_locatario');
    }
    
    public function gerenciadora(){
        return $this->hasOne(Gerenciadora::class,'id_gerenciadora', 'id_gerenciadora');
    }
}
