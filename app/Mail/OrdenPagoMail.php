<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class OrdenPagoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function build()
    {
        $pdf = Pdf::loadView('orden_pago', $this->datos)->setPaper('A4');

        return $this->subject('Recibo de Orden de Pago')
            ->view('opa', $this->datos)
            ->attachData($pdf->output(), 'recibo-pago.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
