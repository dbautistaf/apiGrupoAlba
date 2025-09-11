<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelacionLaboralModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_relacion_labora';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_padron',
        'id_empresa',
        'fecha_alta_empresa',
        'fecha_baja_empresa',
        'id_usuario'
    ];
    
    public function relacionEmpresa()
    {
        return $this->hasOne(EmpresaModelo::class, 'id_empresa', 'id_empresa');
    }
}
