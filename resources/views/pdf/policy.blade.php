<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Póliza DFM-SECURE - {{ $policy->folio }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #92AA74;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 150px;
        }
        .header-table {
            width: 100%;
        }
        .header-table td {
            vertical-align: middle;
        }
        .header-title {
            text-align: right;
            color: #1a2b3c;
        }
        .header-title h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }
        .header-title p {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #666;
        }
        .section-title {
            background-color: #1a2b3c;
            color: #ffffff;
            padding: 8px 15px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .data-table th, .data-table td {
            padding: 10px;
            border: 1px solid #e2e8f0;
            text-align: left;
        }
        .data-table th {
            background-color: #f8fafc;
            width: 30%;
            color: #475569;
            font-weight: bold;
        }
        .data-table td {
            width: 70%;
        }
        .footer {
            margin-top: 50px;
            font-size: 10px;
            color: #64748b;
            text-align: justify;
            line-height: 1.5;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
        }
        .watermark {
            position: absolute;
            top: 30%;
            left: 20%;
            font-size: 100px;
            color: rgba(146, 170, 116, 0.05);
            transform: rotate(-45deg);
            z-index: -1;
            white-space: nowrap;
        }
    </style>
</head>
<body>

    <!-- Watermark -->
    <div class="watermark">DFM SECURE</div>

    <!-- Header -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <img src="{{ public_path('logos/DFM_SECURE_LOGO.png') }}" class="logo" alt="DFM Secure Logo">
                </td>
                <td class="header-title">
                    <h1>Carátula de Póliza</h1>
                    <p>Seguro de Automóviles</p>
                    <p style="font-weight: bold; margin-top: 10px;">Folio: {{ $policy->folio }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Datos de la Póliza -->
    <div class="section-title">Datos de la Póliza</div>
    <table class="data-table">
        <tr>
            <th>Plan Contratado</th>
            <td style="font-weight: bold; color: #92AA74;">{{ $policy->plan->name }}</td>
        </tr>
        <tr>
            <th>Estatus</th>
            <td>
                @php
                    $statusLabel = method_exists($policy->status, 'label') ? $policy->status->label() : $policy->status->value;
                @endphp
                {{ mb_strtoupper($statusLabel) }}
            </td>
        </tr>
        <tr>
            <th>Inicio de Vigencia</th>
            <td>{{ $policy->begin_validity->format('d de M, Y - 12:00 hrs') }}</td>
        </tr>
        <tr>
            <th>Fin de Vigencia</th>
            <td>{{ $policy->end_validity->format('d de M, Y - 12:00 hrs') }}</td>
        </tr>
    </table>

    <!-- Datos del Asegurado -->
    <div class="section-title">Datos del Asegurado</div>
    <table class="data-table">
        <tr>
            <th>Nombre Completo</th>
            <td>{{ $user->name }} {{ $user->father_lastname }} {{ $user->mother_lastname }}</td>
        </tr>
        <tr>
            <th>Correo Electrónico</th>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <th>Teléfono</th>
            <td>{{ $user->phone ?? 'N/A' }}</td>
        </tr>
    </table>

    <!-- Datos del Vehículo -->
    <div class="section-title">Vehículo Asegurado</div>
    <table class="data-table">
        <tr>
            <th>Marca y Modelo</th>
            <td>{{ $policy->vehicle->vehicleModel->brand }} {{ $policy->vehicle->vehicleModel->sub_brand }}</td>
        </tr>
        <tr>
            <th>Versión y Año</th>
            <td>{{ $policy->vehicle->vehicleModel->version }} ({{ $policy->vehicle->vehicleModel->year }})</td>
        </tr>
        <tr>
            <th>Color</th>
            <td>{{ $policy->vehicle->vehicleModel->color }}</td>
        </tr>
        <tr>
            <th>Placas</th>
            <td>{{ mb_strtoupper($policy->vehicle->plate) }}</td>
        </tr>
        <tr>
            <th>Número de Serie (VIN)</th>
            <td>{{ mb_strtoupper($policy->vehicle->vin) }}</td>
        </tr>
    </table>

    <!-- Coberturas -->
    @php
        $info = $policy->plan->info ?? [];
        $danosMat = $info['deducible_danos'] ?? 'N/A';
        $roboT = 'No Amparado';
        if (isset($info['cobertura_vehiculo']['robo_total'])) {
            $roboT = $info['cobertura_vehiculo']['robo_total'] ? ($info['deducible_robo'] ?? '10%') : 'No Amparado';
        }
    @endphp
    <div class="section-title">Coberturas y Deducibles</div>
    <table class="data-table">
        <tr>
            <th>Daños Materiales</th>
            <td>{{ $danosMat }}</td>
        </tr>
        <tr>
            <th>Robo Total</th>
            <td>{{ $roboT }}</td>
        </tr>
        <tr>
            <th>Responsabilidad Civil</th>
            <td>Amparada - Límite Único Combinado</td>
        </tr>
        <tr>
            <th>Gastos Médicos Ocupantes</th>
            <td>Amparada</td>
        </tr>
        <tr>
            <th>Asistencia Legal y Vial</th>
            <td>Amparada 24/7</td>
        </tr>
    </table>

    <!-- Footer Condiciones -->
    <div class="footer">
        <strong>Condiciones Generales DFM-SECURE:</strong><br><br>
        Esta carátula de póliza constituye el recibo oficial de pago y constancia de cobertura. La protección de esta póliza se rige estrictamente por las Condiciones Generales para Seguros de Automóviles de DFM-SECURE, registradas ante las autoridades competentes. Queda expresamente estipulado que la falta de veracidad en las declaraciones del asegurado, así como la conducción en estado de ebriedad o bajo la influencia de drogas, la carencia de licencia de conducir apropiada o el uso del vehículo para fines distintos a los declarados, invalidarán automáticamente cualquier cobertura estipulada en este contrato. En caso de siniestro, es obligación del asegurado reportar inmediatamente a nuestra cabina de atención (800-336-7328) y esperar instrucciones del ajustador antes de mover la unidad, salvo indicación expresa de la autoridad competente. Las coberturas amparadas y exclusiones detalladas están disponibles para su consulta en el portal oficial de DFM-SECURE.
    </div>

</body>
</html>
