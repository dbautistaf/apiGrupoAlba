<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleTipoDocAfiliadoModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_detalle_tipo_doc_afiliado';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;

    protected $fillable = [
        'nombre_archivo',
        'id_padron',
        'id_tipo_documentacion',
        'fecha_carga',
        'observacion'
    ];

    public function tipoDocumentacion()
    {
        return $this->belongsTo(TipoDocumentacionAfiliadoModelo::class, 'id_tipo_documentacion', 'id_tipo_documentacion');
    }
}
