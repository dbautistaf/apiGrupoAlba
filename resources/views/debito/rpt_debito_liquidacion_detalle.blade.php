
                    @foreach ($detalle as $row)
                    <tr>
                        <td>
                            {{ $row->codigo_practica }}
                        </td>
                        <td>{{ $row->practica }}</td>
                        <td>

                            {{ $row->monto_facturado}}
                        </td>
                        <td>
                            {{ $row->monto_aprobado}}
                        </td>
                        <td>
                            {{$row->monto_debitado}}
                        </td>
                        <td>
                            {{ $row->motivo_debito}}
                        </td>
                        <td>
                            {{ $row->observacion_debito }}
                        </td>
                        <td>
                            {{ date('d/m/Y', strtotime($row->fecha_prestacion)) }}
                        </td>
                        <td>
                            {{ $row->dni_afiliado}}
                        </td>
                        <td>
                            {{ $row->afiliado}}
                        </td>
                    </tr>
                    @endforeach