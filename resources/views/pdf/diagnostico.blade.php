<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha de Diagnostico - {{ $order->numero_ticket }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #121212;
            font-size: 11pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 3px solid #e50914;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header table {
            width: 100%;
        }
        .brand-title {
            font-size: 20pt;
            font-weight: bold;
            color: #000000;
        }
        .brand-subtitle {
            font-size: 9pt;
            color: #555555;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .ticket-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 10px;
            text-align: center;
            border-radius: 4px;
        }
        .ticket-number {
            font-size: 14pt;
            font-weight: bold;
            color: #e50914;
        }
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #e50914;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 5px;
            margin-top: 25px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table th, .info-table td {
            padding: 8px;
            text-align: left;
            vertical-align: top;
            border-bottom: 1px solid #f1f3f5;
        }
        .info-table th {
            width: 30%;
            font-weight: bold;
            color: #495057;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: #fafafa;
        }
        .details-table th, .details-table td {
            border: 1px solid #e9ecef;
            padding: 8px 12px;
            text-align: left;
        }
        .details-table th {
            background-color: #f1f3f5;
            font-weight: bold;
            text-transform: capitalize;
            width: 40%;
        }
        .signature-section {
            margin-top: 40px;
            width: 100%;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 48%;
            display: inline-block;
            text-align: center;
            vertical-align: top;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 60px auto 10px auto;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #888888;
            border-top: 1px solid #e9ecef;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <span class="brand-title">IMPORTADORA MARTINEZ</span><br>
                    <span class="brand-subtitle">Servicio Tecnico Especializado en Electronica</span>
                </td>
                <td style="text-align: right; width: 220px;">
                    <div class="ticket-box">
                        <span style="font-size: 8pt; color: #6c757d; text-transform: uppercase;">Orden de Servicio</span><br>
                        <span class="ticket-number">{{ $order->numero_ticket }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 15px;">
                <div style="font-weight: bold; color: #e50914; margin-bottom: 8px; font-size: 10pt; text-transform: uppercase;">Datos del Cliente</div>
                <table style="width: 100%; font-size: 9.5pt;">
                    <tr>
                        <td style="font-weight: bold; width: 35%;">Nombre:</td>
                        <td>{{ $order->cliente->nombre }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">C.I.:</td>
                        <td>{{ $order->cliente->carnet_identidad }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Telefono:</td>
                        <td>{{ $order->cliente->telefono }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 15px; border-left: 1px solid #e9ecef;">
                <div style="font-weight: bold; color: #e50914; margin-bottom: 8px; font-size: 10pt; text-transform: uppercase;">Sucursal de Servicio</div>
                <table style="width: 100%; font-size: 9.5pt;">
                    <tr>
                        <td style="font-weight: bold; width: 35%;">Sede:</td>
                        <td>{{ $order->sucursal->nombre }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Direccion:</td>
                        <td>{{ $order->sucursal->direccion }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Telefono:</td>
                        <td>{{ $order->sucursal->telefono }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="section-title">Detalles del Equipo</div>
    <table class="info-table">
        <tr>
            <th>Categoria / Tipo:</th>
            <td>{{ ucfirst($order->categoria) }}</td>
            <th>Marca / Modelo:</th>
            <td>{{ $order->marca }} / {{ $order->modelo }}</td>
        </tr>
        <tr>
            <th>Nro de Serie:</th>
            <td>{{ $order->serie ?? 'N/A' }}</td>
            <th>Problema Reportado:</th>
            <td>{{ $order->problema_reportado }}</td>
        </tr>
    </table>

    <div class="section-title">Resultados de Revision Tecnica</div>
    <table class="details-table">
        <thead>
            <tr>
                <th>Componente / Punto</th>
                <th>Estado Diagnosticado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detalles as $component => $state)
                @if($component !== 'observaciones')
                    <tr>
                        <td style="font-weight: bold;">{{ str_replace('_', ' ', $component) }}</td>
                        <td>
                            @if($state === 'buen_estado')
                                Buen estado
                            @elseif($state === 'mal_estado')
                                Mal estado
                            @elseif($state === 'no_corresponde')
                                No corresponde
                            @else
                                {{ $state ?: 'Sin evaluar' }}
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td style="font-weight: bold; background-color: #f1f3f5;">Diagnostico y Observaciones</td>
                <td style="background-color: #fafafa;">{{ $detalles['observaciones'] ?? 'Sin observaciones' }}</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 25px;">
        <div style="background-color: #f8f9fa; border: 1px solid #e50914; padding: 15px; border-radius: 4px; text-align: center;">
            <span style="font-size: 9pt; color: #555555; text-transform: uppercase;">Presupuesto Estimado:</span><br>
            <span style="font-size: 18pt; font-weight: bold; color: #e50914;">Bs. {{ number_format($order->costo_estimado, 2) }}</span>
        </div>
    </div>

    <div class="signature-section">
        <div class="signature-box" style="float: left;">
            @if($order->firma_checkin_base64)
                <img src="{{ $order->firma_checkin_base64 }}" style="max-height: 55px; max-width: 150px; display: block; margin: 0 auto;">
            @endif
            <div class="signature-line"></div>
            <span style="font-size: 9pt; font-weight: bold;">{{ $order->cliente->nombre }}</span><br>
            <span style="font-size: 8pt; color: #6c757d;">Firma Conformidad Cliente</span>
        </div>

        <div class="signature-box" style="float: right;">
            <div style="height: 55px; text-align: center; line-height: 55px; font-style: italic; color: #6c757d; font-size: 9pt;">
                Firmado digitalmente por Sede
            </div>
            <div class="signature-line"></div>
            <span style="font-size: 9pt; font-weight: bold;">{{ $admin ? $admin->nombre_completo : 'Administracion Central' }}</span><br>
            <span style="font-size: 8pt; color: #6c757d;">Responsable Sucursal - Importadora Martinez</span>
        </div>
    </div>

    <div class="footer">
        IMPORTADORA MARTINEZ &copy; {{ date('Y') }} &mdash; Sistema de Gestion Blindado Legalmente
    </div>

</body>
</html>
