<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Prestadores\PrestadorEntity;
use App\Models\Prestadores\TipoInputacionesContablesEntity;

class TesCashEgreso extends Model
{
    use HasFactory;
    protected $table = 'tb_tes_cash_egresos';
    protected $primaryKey = 'id_cash_egresos';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_imputacion',
        'id_prestador',
        'detalle',
        'fecha_emision',
        'fecha_cobro',
        'numero_comprobante',
        'pendiente_abonar',
        'abonado',
        'usuario_crea',
    ];


    public function prestador(): BelongsTo
    {
        return $this->belongsTo(PrestadorEntity::class, 'id_prestador', 'cod_prestador');
    }

    public function tipoImputacion(): BelongsTo
    {
        return $this->belongsTo(TipoInputacionesContablesEntity::class, 'id_tipo_imputacion', 'id_tipo_imputacion_contable');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_crea', 'cod_usuario');
    }
}
