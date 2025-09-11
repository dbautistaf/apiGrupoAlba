<?php

namespace App\Models;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferenciasModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_transferencias';
    protected $primaryKey = 'id_transferencia';
    public $timestamps = false;
    protected $fillable = [
        'organ',
        'codconc',
        'importe',
        'inddbcr',
        'fecproc',
        'fecrec',
        'cuitcont',
        'periodo',
        'id_tranf',
        'cuitapo',
        'banco',
        'codsuc',
        'zona',
        'periodo_tranf',
        'fecha_proceso',
        'id_usuario'
    ];

    public function PadronAfil()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'cuil_tit', 'cuitapo');
    }

    public function Empresa()
    {
        return $this->hasOne(EmpresaModelo::class, 'cuit', 'cuitcont');
    }
}
