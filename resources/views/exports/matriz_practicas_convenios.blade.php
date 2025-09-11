<table>
    <thead>
        <tr>
            <th>CODIGO PRACTICA</th>
            <th>NOMBRE PRACTICA</th>
            <th>VALOR GASTO</th>
            <th>VIGENCIA DESDE</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detalle as $item)
            <tr>
                <td>{{ $item['codigo_practica'] }}</td>
                <td>{{ $item['nombre_practica'] }}</td>
                <td>{{ $item['monto_gastos'] }}</td>
                <td>{{ $item['fecha_inicio'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
