<?php

namespace App\Models\prestadores;

use App\Models\ubigeo\UbigeoLocalidadesEntity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestadorEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_prestador';
    protected $primaryKey = 'cod_prestador';
    public $timestamps = false;

    protected $fillable = [
        'cuit',
        'razon_social',
        'nombre_fantasia',
        'fecha_alta',
        'fecha_baja',
        'numero_inscripcion_super',
        'celular',
        'codigo_postal_telefono',
        'email',
        'email1',
        'email2',
        'direccion',
        'observaciones',
        'cod_tipo_prestador',
        'cod_tipo_impuesto',
        'cod_tipo_iva',
        'departamento',
        'cod_localidad',
        'cod_usuario',
        'vigente',
        'id_regimen',
        'id_tipo_efector'
    ];

    public function tipoPrestador()
    {
        return $this->hasOne(TipoPrestadorEntity::class, 'cod_tipo_prestador', 'cod_tipo_prestador');
    }

    public function tipoImpuesto()
    {
        return $this->hasOne(TipoImpuestosGananciasEntity::class, 'cod_tipo_impuesto', 'cod_tipo_impuesto');
    }

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
        return $this->hasOne(DatosBancariosPrestadorEntity::class, 'cod_prestador', 'cod_prestador');
    }

    public function metodoPago()
    {
        return $this->hasOne(MetodoPagoPrestadorEntity::class, 'cod_prestador', 'cod_prestador');
    }

    public function tiposImputaciones()
    {
        return $this->hasMany(PrestadorImputacionesContablesEntity::class, 'cod_prestador', 'cod_prestador');
    }

    public function usuario()
    {
        return $this->hasOne(User::class, 'cod_usuario', 'cod_usuario');
    }
}
