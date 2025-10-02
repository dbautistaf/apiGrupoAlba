<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Orden de Pago</title>
</head>

<body>
    <h2>Orden de Pago #{{ $datos['comprobante_nro'] }}</h2>
    <p><strong>Fecha de Emisión:</strong> {{ $datos['fecha_emision'] }}</p>
    <p><strong>Proveedor:</strong> {{ $datos['nombre_proveedor'] }}</p>
    <p><strong>Total:</strong> ${{ number_format($datos['total'], 2) }}</p>
    {{-- <p><strong>Observaciones:</strong> {{ $datos['observaciones'] }}</p> --}}
    <p>Adjunto encontrarás el comprobante en formato PDF.</p>
</body>

</html>