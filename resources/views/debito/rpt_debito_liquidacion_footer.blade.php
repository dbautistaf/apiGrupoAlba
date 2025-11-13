</tbody>
</table>
</div>
<div class="footer">
    <div class="observacion">
        <spasn>Observaciones de Auditoria</spasn>
        <p>Se cubrieron los servicios solicitados, incluyendo consultas, estudios y
            medicamentos. Los costos están detallados y se aplicaron los descuentos
            correspondientes.</p>
    </div>
    <div class="footer-table">
        <p>Resumen</p>
        <table>
            <tr>
                <td>Comprobante:</td>
                <td>{{ number_format($factura->subtotal ?? 0, 2, ',', '.') }} </td>
            </tr>
            <tr>
                <td>Debitos:</td>
                <td>{{ number_format($factura->total_debitado ?? 0, 2, '.', '') }}</td>
            </tr>
            <tr>
                <td>IVA:</td>
                <td>{{ number_format($factura->total_iva ?? 0, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total a Pagar:</td>
                <td>
                    {{ number_format(($factura->total_neto ?? 0) - (($factura->total_debitado ?? 0) != 0 ? $factura->total_debitado : 0), 2, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>
    <br>
    <br>
</div>
</div>

</body>

</html>
