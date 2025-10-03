<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Utils\ManejadorDeArchivosUtils;
use App\Models\Soporte\SoporteArchivoModelo;
use App\Models\Soporte\SoporteHistorialTicketModelo;
use App\Models\Soporte\SoporteTicketsModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Soporte\SoporteTicketsModelo as SoporteSoporteTicketsModelo;
use App\Models\User;

class TicketSoporteController extends Controller
{
    //
    public function postSaveTicketsSoporte(Request $request, ManejadorDeArchivosUtils $storageFile)
    {
        $now = new \DateTime('now', new \DateTimeZone('America/Argentina/Buenos_Aires'));
        $msg = '';

        if ($request->id_ticket == '') {
            // Crear el ticket
            $user = Auth::user();
            $ticket = SoporteSoporteTicketsModelo::create([
                // 'titulo' => $request->titulo,
                'observaciones' => $request->observaciones,
                'asignado_por' => $request->asignado_por,
                'fecha_apertura' => $now->format('Y-m-d H:i:s'),
                'fecha_respuesta' => null,
                'cliente' => $request->cliente,
                'id_prioridad' => $request->id_prioridad,
                'id_categoria' => $request->id_categoria,
                'id_tipo_producto' => $request->id_tipo_producto,
                'id_estado' => $request->id_estado,
                'id_instancia' => $request->id_instancia,
                'id_Usuario' => $user->cod_usuario,
                'id_tarea' => null,
            ]);


            SoporteHistorialTicketModelo::create([
                'id_ticket' => $ticket->id_ticket, // ID del ticket recién creado
                'estado_anterior' => null,        // No hay estado anterior (es el primer registro)
                'estado_nuevo' => $request->id_estado, // Estado inicial del ticket
                'fecha_cambio' => now(),
                'id_encargado_anterior' => null,     // Aún no hay encargado anterior
                'id_encargado_nuevo' => null,     // Usuario que creó el ticket
                'comentario' => 'Ticket creado.'
            ]);
            //Guardado de archivos adjuntos
            if (count($request->archivos) > 0) {
                $archivosAdjuntos = $storageFile->findByCargaMasivaArchivos(
                    "TICKET_" . $ticket->id_ticket,
                    'tickets/archivos',
                    $request
                );

                $this->guardarArchivosAdjuntosTickets($archivosAdjuntos, $ticket->id_ticket);
            }



            $msg = 'Ticket registrado correctamente';
        }

        return response()->json(['message' => $msg], 200);
    }

    private function guardarArchivosAdjuntosTickets(array $archivosAdjuntos, int $idTicket)
    {
        foreach ($archivosAdjuntos as $archivo) {
            SoporteArchivoModelo::create([
                'id_ticket' => $idTicket,
                'nombre_original' => $archivo['nombre'],
                'ruta' => str_replace('public/', '', $archivo['ruta']),
                'mime' => null,
                'tamaño' => null,
            ]);
        }
    }

    public function getListTickets($sistema)
    {
        $query = SoporteTicketsModelo::with('Estado')
            ->with('Prioridad')
            ->with('Instancia')
            ->with('Categoria')
            ->with('Archivo')
            ->where('cliente', $sistema)
            ->orderBy('fecha_apertura', 'desc')
            ->get();
        return response()->json($query, 200);
    }

    public function getLitsTicketGeneral(Request $request)
    {
        $query = SoporteTicketsModelo::with([
            'Estado',
            'Prioridad',
            'Instancia',
            'Categoria',
            'Asignados',
            'Archivo'
        ])
            ->where('cliente', 'ALBA')
            ->orderByDesc('fecha_apertura');
            

        if ($request->filled('prioridad')) {
            $query->where('id_prioridad', $request->prioridad);
        }
        

        if ($request->filled('categoria')) {
            $query->where('id_categoria', $request->categoria);
        }
        

        if ($request->filled('estado')) {
            $query->where('id_estado', $request->estado);
        }
        
        if ($request->filled('numero')) {
            $query->where('id_ticket', $request->numero);
        }
        
        if ($request->desde && $request->hasta) {
            $query->whereBetween('fecha_apertura', [$request->desde, $request->hasta]);
        }
        return response()->json($query->get(), 200);
    }

    public function getIdticket($id)
    {
        $query = SoporteTicketsModelo::with('Archivo')->where('id_ticket', $id)->first();

        $usuario = User::where('cod_usuario', $query->id_Usuario)->first();
        $query['usuario'] = $usuario;
        return response()->json($query, 200);
    }

    public function updateAsignacion(Request $request)
    {

        $request->validate([
            'id_ticket' => 'required|integer',
            'id_encargado' => 'required|integer',
            'id_estado' => 'nullable|integer', // Estado opcional
            'comentario' => 'nullable|string', // Comentario opcional
        ]);

        // Obtener el ticket actual
        $ticket = SoporteTicketsModelo::where('id_ticket', $request->id_ticket)->first();

        if (!$ticket) {
            return response()->json(['message' => 'Ticket no encontrado'], 404);
        }

        // Registrar el cambio en el historial
        SoporteHistorialTicketModelo::create([
            'id_ticket' => $request->id_ticket,
            'estado_anterior' => $ticket->id_estado, // Estado anterior
            'estado_nuevo' => $request->id_estado ?? $ticket->id_estado, // Estado nuevo (si se proporciona)
            'fecha_cambio' => now(),
            'id_encargado_anterior' => $ticket->id_encargado, // Encargado anterior
            'id_encargado_nuevo' => $request->id_encargado,   // Encargado nuevo
            'comentario' => $request->comentario ?? 'Sin comentario' // Guardar el comentario
        ]);

        // Actualizar el ticket
        $ticket->update([
            'id_encargado' => $request->id_encargado,
            'id_estado' => $request->id_estado ?? $ticket->id_estado // Actualizar estado solo si se proporciona
        ]);

        return response()->json(['message' => 'Tarea asignada y estado actualizado correctamente'], 200);
    }

    public function updateEstado(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'id_ticket' => 'required|integer',
            'id_estado' => 'required|integer',
            'comentario' => 'nullable|string', // El comentario es opcional
        ]);

        // Obtener el ticket actual
        $ticket = SoporteTicketsModelo::where('id_ticket', $request->id_ticket)->first();

        if (!$ticket) {
            return response()->json(['message' => 'Ticket no encontrado'], 404);
        }

        // Obtener el estado anterior
        $estadoAnterior = $ticket->id_estado;

        // Registrar el cambio en el historial
        SoporteHistorialTicketModelo::create([
            'id_ticket' => $request->id_ticket,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $request->id_estado,
            'fecha_cambio' => now(),
            'id_encargado_anterior' => $ticket->id_encargado, // Mantener el encargado anterior
            'id_encargado_nuevo' => $ticket->id_encargado,    // Mantener el encargado nuevo
            'comentario' => $request->comentario ?? 'Sin comentario' // Guardar el comentario
        ]);

        // Actualizar el ticket
        $ticket->update(['id_estado' => $request->id_estado]);

        return response()->json(['message' => 'Estado del ticket actualizado correctamente'], 200);
    }

    public function getFechaAndResponsable(Request $request)
    {
        if ($request->responsable != '' && $request->desde == '' && $request->hasta == '') {
            $query = SoporteTicketsModelo::with('Estado')->with('Prioridad')->with('Instancia')->with('Categoria')->with('Asignados')->where('id_encargado', $request->responsable)->get();
            return response()->json($query, 200);
        } elseif ($request->responsable != '' && $request->desde != '' && $request->hasta != '') {
            $query = SoporteTicketsModelo::with('Estado')->with('Prioridad')->with('Instancia')->with('Categoria')->with('Asignados')->where('id_encargado', $request->responsable)->whereBetween('fecha_apertura', [$request->desde, $request->hasta])->get();
            return response()->json($query, 200);
        } elseif ($request->responsable == '' && $request->desde != '' && $request->hasta != '') {
            $query = SoporteTicketsModelo::with('Estado')->with('Prioridad')->with('Instancia')->with('Categoria')->with('Asignados')->whereBetween('fecha_apertura', [$request->desde, $request->hasta])->get();
            return response()->json($query, 200);
        }
    }
    public function getHistorial($idTicket)
    {
        // Obtener el historial del ticket
        $historial = SoporteHistorialTicketModelo::with('estadoAnterior')
            ->with('estadoNuevo')
            ->with('encargadoAnterior') // Cargar el encargado anterior
            ->with('encargadoNuevo')   // Cargar el encargado nuevo
            ->where('id_ticket', $idTicket)
            ->orderBy('fecha_cambio', 'desc') // Ordenar por fecha más reciente
            ->get();

        return response()->json($historial, 200);
    }

    public function getArchivosPorTicket($id)
    {
        $archivos = SoporteArchivoModelo::where('id_ticket', $id)->get();
        return response()->json($archivos, 200);
    }



    public function getArchivoAdjunto(ManejadorDeArchivosUtils $storageFile, Request $request)
    {
        $path = "tickets/archivos/";
        // $data = $pago->findById($request->id);
        $anioTrabaja = $request->fecha_registra;
        $path .= "{$anioTrabaja}/$request->nombre_archivo";

        return $storageFile->findByObtenerArchivo($path);
    }
}
