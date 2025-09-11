<table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Descripcion</th>
            <th>Valor</th>
            <th>Vigencia</th>
            <th>Error</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($practicas as $practica)
            <tr>
                <td>{{ $practica['codigo_practica'] }}</td>
                <td>{{ $practica['descripcion_practica'] }}</td>
                <td>{{ $practica['valor'] }}</td>
                <th>{{ $practica['vigencia'] }}</th>
                <td style="color:red;">{{ $practica['obs'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
