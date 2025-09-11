<?php

namespace App\Models;

use App\Models\Filiales\FilialesEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MesaEntradaModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_mesa_entrada';
    protected $primaryKey = 'cod_mesa';
    public $timestamps = false;

    protected $fillable = [
        'cod_tipo_documentacion',
        'emisor',
        'nro_factura',
        'importe',
        'fecha_documentacion',
        'fecha_carga',
        'observaciones',
        'cod_tipo_area',
        'cod_sindicato',
        'cod_usuario',
        'archivo'
    ];

    public function tipoDocumento()
    {
        return $this->hasOne(TipoDocumentacionModelo::class, 'cod_tipo_documentacion', 'cod_tipo_documentacion');
    }

    public function tipoArea()
    {
        return $this->hasOne(TipoAreaModelo::class, 'cod_tipo_area', 'cod_tipo_area');
    }

    public function sindicato()
    {
        return $this->hasOne(FilialesEntity::class, 'cod_sindicato', 'cod_sindicato');
    }
}
