<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Autorización de Prestación Médica</title>
    <style>
        @page {
            margin: 8px 12px;
        }
        body {
            font-family: 'Quicksand', 'centurygothic', 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .header-logo {
            font-size: 24px;
            font-weight: bold;
        }
        .header-logo img {
            max-height: 45px;
            max-width: 160px;
            width: auto;
            height: auto;
        }
        .logo-bene { color: #0f766e; } /* Teal-700 */
        .logo-salud { color: #0369a1; } /* Sky-700 */
        
        .header-center {
            font-size: 12px;
            font-weight: bold;
            color: #374151;
            text-align: center;
            vertical-align: bottom;
            padding-bottom: 4px;
        }
        .header-page {
            font-size: 10px;
            color: #6b7280;
            text-align: right;
            vertical-align: bottom;
            padding-bottom: 4px;
        }
        
        /* Box container simulating fieldset */
        .fieldset-box {
            border: 1px solid #9ca3af;
            border-radius: 6px;
            margin-bottom: 10px;
            padding: 8px 12px;
            position: relative;
        }
        .fieldset-legend {
            font-weight: bold;
            font-size: 10px;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 2px;
        }
        
        .grid-table {
            width: 100%;
            border-collapse: collapse;
        }
        .grid-table td {
            padding: 3px 4px;
            vertical-align: top;
        }
        
        .label {
            font-weight: bold;
            color: #374151;
            width: 130px;
        }
        .value {
            color: #111827;
        }
        
        /* Practices Table */
        .practices-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border: 1px solid #9ca3af;
            border-radius: 4px;
            overflow: hidden;
        }
        .practices-table th {
            background-color: #374151;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            padding: 6px 8px;
            text-align: left;
            border: 1px solid #374151;
        }
        .practices-table td {
            padding: 6px 8px;
            border: 1px solid #9ca3af;
            font-size: 10.5px;
        }
        
        .status-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .status-text {
            font-size: 13px;
            font-weight: bold;
        }
        .status-authorized {
            color: #0f766e;
        }
        .status-pending {
            color: #d97706;
        }
        .status-rejected {
            color: #b91c1c;
        }
        
        /* Observations & Stamp */
        .obs-stamp-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #9ca3af;
            border-radius: 6px;
            margin-bottom: 10px;
        }
        .obs-cell {
            width: 70%;
            padding: 8px 12px;
            vertical-align: top;
            border-right: 1px solid #9ca3af;
        }
        .stamp-cell {
            width: 30%;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
            background-color: #f9fafb;
        }
        .stamp-box {
            border: 2px dashed #0f766e;
            color: #0f766e;
            padding: 10px;
            font-weight: bold;
            font-size: 12px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .stamp-box-pending {
            border: 2px dashed #d97706;
            color: #d97706;
        }
        .stamp-box-rejected {
            border: 2px dashed #b91c1c;
            color: #b91c1c;
        }
        .legal-notice {
            background-color: #f3f4f6;
            padding: 4px 8px;
            font-size: 9px;
            color: #4b5563;
            text-align: justify;
            border-top: 1px solid #9ca3af;
        }
        
        /* Signatures footer */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .signature-col {
            width: 33.33%;
            border: 1px solid #9ca3af;
            padding: 8px;
            height: 105px;
            vertical-align: bottom;
            text-align: center;
            font-size: 9px;
            color: #4b5563;
        }
        .signature-col-left {
            text-align: left;
            vertical-align: top;
        }
        .signature-line {
            border-top: 1px solid #9ca3af;
            margin-top: 10px;
            padding-top: 4px;
            text-transform: uppercase;
            font-weight: bold;
            color: #1f2937;
        }
        
        .rn-highlight-box {
            border: 1.5px solid #0369a1;
            background-color: #f0f9ff;
        }
        .rn-highlight-legend {
            color: #0369a1;
            border-bottom: 1px dashed #bae6fd;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td width="30%">
                <span class="header-logo">
                    @if ($id_locatario == 1)
                        <img src="{{ storage_path('app/public/images/bon_baja.jpeg') }}">
                    @elseif ($id_locatario == 2)
                        <img src="{{ storage_path('app/public/images/sembrar_baja.jpeg') }}">
                    @elseif ($id_locatario == 3)
                        <img src="{{ storage_path('app/public/images/bene_baja.jpeg') }}">
                    @else
                        <img src="{{ storage_path('app/public/images/alba.png') }}">
                    @endif
                </span>
            </td>
            <td class="header-center" width="50%">
                CENTRO OPERATIVO: {{ $centro_operativo }}
            </td>
            <td class="header-page" width="20%">
                Pagina: 1 / 1
            </td>
        </tr>
    </table>

    <!-- Tramite & Fecha Box -->
    <div class="fieldset-box">
        <table class="grid-table">
            <tr>
                <td width="35%">
                    <span class="label">Tipo de Trámite:</span>
                    <span class="value" style="text-transform: uppercase;">{{ $tipo_tramite }}</span>
                </td>
                <td width="35%">
                    <span class="label">Tramite Nro.:</span>
                    <span class="value" style="font-weight: bold;">{{ $tramite_nro }}</span>
                </td>
                <td width="30%">
                    <span class="label">Fecha:</span>
                    <span class="value">{{ $fecha }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Beneficiario Box -->
    <div class="fieldset-box">
        <div class="fieldset-legend">Datos del Beneficiario</div>
        <table class="grid-table">
            <tr>
                <td width="55%">
                    <span class="label">Nombre y Apellidos:</span>
                    <span class="value" style="text-transform: uppercase;">{{ $beneficiario_nombre }}</span>
                </td>
                <td width="45%">
                    <span class="label">Nro. Beneficiario:</span>
                    <span class="value">{{ $beneficiario_nro }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">Documento:</span>
                    <span class="value">{{ $beneficiario_dni }}</span>
                </td>
                <td>
                    <span class="label">Beneficiario:</span>
                    <span class="value" style="text-transform: uppercase;">{{ $beneficiario_tipo }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">Edad:</span>
                    <span class="value">{{ $beneficiario_edad }}</span>
                </td>
                <td>
                    <span class="label">Localidad:</span>
                    <span class="value" style="text-transform: uppercase;">{{ $beneficiario_localidad }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <span class="label">Obra Social:</span>
                    <span class="value" style="text-transform: uppercase;">{{ $beneficiario_obrasocial }}</span>
                </td>
            </tr>
        </table>
    </div>

    @if($is_rn)
    <!-- Newborn Box (Rendered only if newborn authorization) -->
    <div class="fieldset-box rn-highlight-box">
        <div class="fieldset-legend rn-highlight-legend">Datos del Recién Nacido</div>
        <table class="grid-table">
            <tr>
                <td width="55%">
                    <span class="label" style="color: #0369a1;">Nombre y Apellidos RN:</span>
                    <span class="value" style="text-transform: uppercase; font-weight: bold; color: #0369a1;">{{ $rn_nombre }}</span>
                </td>
                <td width="45%">
                    <span class="label" style="color: #0369a1;">DNI Recién Nacido:</span>
                    <span class="value" style="font-weight: bold; color: #0369a1;">{{ $rn_dni }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label" style="color: #0369a1;">Fecha Nacimiento RN:</span>
                    <span class="value" style="color: #0369a1;">{{ $rn_fecha_nac }}</span>
                </td>
                <td>
                    <span class="label" style="color: #0369a1;">Madre del RN:</span>
                    <span class="value" style="text-transform: uppercase; color: #0369a1;">{{ $beneficiario_nombre }} (DNI: {{ $beneficiario_dni }})</span>
                </td>
            </tr>
        </table>
    </div>
    @endif

    <!-- Prestador Box -->
    <div class="fieldset-box">
        <div class="fieldset-legend">Prestador Efector</div>
        <table class="grid-table">
            <tr>
                <td colspan="2">
                    <span class="label">Nombre:</span>
                    <span class="value" style="text-transform: uppercase;">{{ $prestador_nombre }}</span>
                </td>
            </tr>
            <tr>
                <td width="60%">
                    <span class="label">Dirección:</span>
                    <span class="value" style="text-transform: uppercase;">{{ $prestador_direccion }}</span>
                </td>
                <td width="40%">
                    <span class="label">Teléfono:</span>
                    <span class="value">{{ $prestador_telefono }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Practices Table -->
    <table class="practices-table">
        <thead>
            <tr>
                <th width="70%">Prestación</th>
                <th width="15%">Código</th>
                <th width="15%" style="text-align: center;">Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($practicas as $row)
            <tr>
                <td>{{ $row['nombre'] }}</td>
                <td>{{ $row['codigo'] }}</td>
                <td style="text-align: center;">{{ $row['cantidad'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Diagnosis Box -->
    <div class="fieldset-box">
        <div class="fieldset-legend">Diagnóstico</div>
        <div style="font-size: 11px; font-weight: bold; padding: 2px 0;">
            (*) {{ $diagnostico }}
        </div>
    </div>

    <!-- Audit state -->
    <table class="status-container">
        <tr>
            <td width="50%" class="status-text">
                Trámite: 
                <span class="{{ $estado_class }}">
                    {{ $estado_tramite }}
                </span>
            </td>
            <td width="50%" class="status-text" style="text-align: right; font-size: 11px; font-weight: normal; color: #4b5563;">
                Fecha de Autorización: <span style="font-weight: bold; color: #1f2937;">{{ $fecha_autorizacion }}</span>
            </td>
        </tr>
    </table>

    <!-- Observations & Stamp Box -->
    <table class="obs-stamp-table">
        <tr>
            <td class="obs-cell">
                <div style="font-weight: bold; margin-bottom: 4px; text-transform: uppercase; font-size: 10px;">Observaciones</div>
                <div style="font-size: 10.5px;">
                    (*) {{ $observaciones ?: 'Sin observaciones' }}
                </div>
            </td>
            <td class="stamp-cell">
                <div class="stamp-box {{ $stamp_class }}">
                    {{ $stamp_text }}
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="legal-notice">
                Ad referéndum de Auditoría Médica Central. Esta autorización debe adjuntarse a la facturación, junto con una copia de los informes o historia clínica. Validez de la Autorización: 30 días
            </td>
        </tr>
    </table>

    <!-- Footer Signatures -->
    <table class="signatures-table">
        <tr>
            <td class="signature-col signature-col-left">
                <div style="font-size: 10px; font-weight: bold; color: #111827;">{{ $operador_nombre }}</div>
                <div style="color: #6b7280; font-style: italic; margin-bottom: 25px;">Operador</div>
                <div class="signature-line">Firma y sello del Medico Auditor</div>
            </td>
            <td class="signature-col">
                <div class="signature-line">Firma del Beneficiario</div>
            </td>
            <td class="signature-col signature-col-left">
                <div style="margin-bottom: 40px; color: #4b5563;">Fecha de atención: ......../......../........</div>
                <div class="signature-line" style="text-align: center;">Firma, sello y matricula de Efector</div>
            </td>
        </tr>
    </table>

</body>
</html>
