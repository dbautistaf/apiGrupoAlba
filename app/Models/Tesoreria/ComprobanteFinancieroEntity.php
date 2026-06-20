<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComprobanteFinancieroEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_comprobante_financiero';
    protected $primaryKey = 'id_comprobante';
    public $timestamps = false;

    protected $fillable = [
        'tipo_comprobante',    // VARCHAR(50) (Factura A, Recibo, Ticket)
        'cuit_emisor',         // VARCHAR(20) / BIGINT
        'razon_social_emisor', // VARCHAR(150)
        'url_archivo',         // VARCHAR(255) / TEXT
        'monto_total',         // DECIMAL(15,2)
        'datos_extraidos_ocr', // JSON / TEXT (Guardando la lectura de Cygnus OCR)
        'id_usuario_alta',     // BIGINT / INT
        'fecha_alta'           // DATETIME / TIMESTAMP
    ];

    protected $casts = [
        'datos_extraidos_ocr' => 'array'
    ];
}
