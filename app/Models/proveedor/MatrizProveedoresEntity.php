<?php

namespace App\Models\proveedor;

use App\Models\prestadores\TipoCondicionIvaEntity;
use App\Models\ubigeo\UbigeoLocalidadesEntity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatrizProveedoresEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_proveedor';
    protected $primaryKey = 'cod_proveedor';
    public $timestamps = false;

    protected $fillable = [
        'cuit',
        'razon_social',
        'nombre_fantasia',
        'fecha_alta',
        'fecha_baja',
        'celular',
        'codigo_postal_telefono',
        'email',
        'direccion',
        'observaciones',
        'cod_tipo_impuesto',
        'cod_tipo_iva',
        'departamento',
        'cod_localidad',
        'cod_usuario',
        'vigente',
        'id_regimen',
        'id_proveedor_tipo',
    ];

    public function tipoIva()
    {
        return $this->hasOne(TipoCondicionIvaEntity::class, 'cod_tipo_iva', 'cod_tipo_iva');
    }
    public function localidad()
    {
        return $this->hasOne(UbigeoLocalidadesEntity::class, 'cod_localidad', 'cod_localidad');
    }

    public function datosBancarios()
    {
        return $this->hasOne(DatosBancariosEntity::class, 'cod_proveedor', 'cod_proveedor');
    }

    public function metodoPago()
    {
        return $this->hasOne(MetodoPagoProveedorEntity::class, 'cod_proveedor', 'cod_proveedor');
    }

    public function usuario()
    {
        return $this->hasOne(User::class, 'cod_usuario', 'cod_usuario');
    }
}
