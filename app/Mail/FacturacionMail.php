<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function build()
    {
        $pdf = Pdf::loadView('comprobante-facturacion', $this->datos)->setPaper('A4');

        return $this->subject('Facturación')
            ->view('facturacion', $this->datos) // Crea una vista para el correo
            ->attachData($pdf->output(), 'facturación.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
