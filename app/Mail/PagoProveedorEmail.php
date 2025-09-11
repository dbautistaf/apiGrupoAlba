<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class PagoProveedorEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function build()
    {
        $pdf = Pdf::loadView('pagoProveedor', $this->datos)->setPaper('A4');

        $tipoEntidad = $this->datos['tipo_entidad'] ?? 'Proveedor';
        $subject = 'Comprobante de Pago a ' . $tipoEntidad;

        return $this->subject($subject)
            ->view('pagoProveedor', $this->datos)
            ->attachData($pdf->output(), 'comprobante-pago-' . strtolower($tipoEntidad) . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
