<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Facturación {{ $datos['periodo'] }}</title>
</head>

<body>
    <h2>Facturació periodo #{{ $datos['periodo'] }}</h2>
    <p><strong>Fecha de Emisión:</strong> {{ $datos['fecha_emision'] }}</p>
    <p><strong>Fecha de Vencimiento:</strong> {{ $datos['fecha_vencimiento'] }}</p>
    <p><strong>Nombre / Razón Social:</strong> {{ $datos['nombre_proveedor'] }} - {{ $datos['cuit_proveedor'] }}</p>
    <p><strong>Total:</strong> ${{ number_format($datos['total'], 2) }}</p>
    <p>Adjunto encontrarás el comprobante en formato PDF.</p>
</body>

</html>