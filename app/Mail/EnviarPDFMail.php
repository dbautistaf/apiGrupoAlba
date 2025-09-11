<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnviarPDFMail extends Mailable
{
    use Queueable, SerializesModels;
    public $factura;
    public $archivos;
    public $asunto;
    public $mensaje;
    public function __construct($archivos, $factura, $asunto, $mensaje)
    {
        $this->archivos = $archivos;
        $this->factura = $factura;
        $this->asunto = $asunto;
        $this->mensaje = $mensaje;
    }

    public function build(): EnviarPDFMail
    {
        $correo = $this->view('debito', [$this->factura, $this->mensaje])
            ->from(env('MAIL_USERNAME'), env('MAIL_USERNAME'))
            ->subject($this->asunto);

        foreach ($this->archivos as $archivo) {
            $correo->attach($archivo['path'], [
                'as' => $archivo['nombre'] . '.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        return $correo;
    }
}
