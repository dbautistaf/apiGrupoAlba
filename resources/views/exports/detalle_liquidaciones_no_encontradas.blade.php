<table>
    <thead>
        <tr>
            <th>Observación</th>
            <th>Código</th>
            <th>Fecha Prestacion</th>
            <th>Monto Facturado</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detalle as $item)
            <tr>
                <td>{{ $item['error'] }}</td>
                <td>{{ $item['codigo'] }}</td>
                <td>{{ $item['fecha_prestacion'] }}</td>
                <td>{{ $item['monto_facturado'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
