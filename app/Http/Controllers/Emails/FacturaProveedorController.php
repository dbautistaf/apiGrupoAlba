<?php
namespace App\Http\Controllers\Emails;
use App\Http\Controllers\facturacion\repository\FacturaRepository;
use App\Mail\FacturaProveedorMail;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;

class FacturaProveedorController extends Controller
{

    public function getEnviarMail(Request $request, FacturaRepository $facturaRepository)
    {
        $data = $facturaRepository->findById($request->id);

        Mail::to($request->email)->send(new FacturaProveedorMail($data));

        return response()->json([
            'success' => true,
            'message' => 'Email enviado correctamente'
        ]);
    }
}
