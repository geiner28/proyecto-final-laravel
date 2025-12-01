<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico Médico</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
            padding: 40px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #3b82f6;
            font-size: 28pt;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 10pt;
        }
        .document-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #f3f4f6;
            padding: 8px 12px;
            font-size: 13pt;
            font-weight: bold;
            color: #1f2937;
            border-left: 4px solid #3b82f6;
            margin-bottom: 12px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 35%;
            color: #4b5563;
        }
        .content-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 10px 0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 40px;
            right: 40px;
            text-align: center;
            font-size: 9pt;
            color: #999;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        .page-number:after {
            content: counter(page);
        }
        .signature-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
        }
        .signature-box {
            text-align: center;
            margin-top: 60px;
        }
        .signature-line {
            border-top: 2px solid #333;
            width: 250px;
            margin: 0 auto 5px;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80pt;
            color: rgba(59, 130, 246, 0.05);
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="watermark">MEDICITAS</div>

    <div class="header">
        <h1>🏥 MediCitas</h1>
        <p>Sistema de Gestión Médica</p>
        <p>Consultas y Diagnósticos Profesionales</p>
    </div>

    <div class="document-title">📋 Diagnóstico Médico</div>

    <div class="section">
        <div class="section-title">👤 Información del Paciente</div>
        <table class="info-table">
            <tr>
                <td>Nombre Completo:</td>
                <td><strong>{{ $diagnostic->patient_name }}</strong></td>
            </tr>
            <tr>
                <td>Cédula / Documento:</td>
                <td><strong>{{ $diagnostic->cedula_paciente }}</strong></td>
            </tr>
            <tr>
                <td>Correo Electrónico:</td>
                <td>{{ $diagnostic->patient_email }}</td>
            </tr>
            @if($diagnostic->telefono_paciente)
            <tr>
                <td>Teléfono:</td>
                <td>{{ $diagnostic->telefono_paciente }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="section">
        <div class="section-title">🩺 Información de la Consulta</div>
        <table class="info-table">
            <tr>
                <td>Fecha de Consulta:</td>
                <td><strong>{{ $diagnostic->fecha_consulta->format('d/m/Y') }}</strong></td>
            </tr>
            <tr>
                <td>Médico Tratante:</td>
                <td><strong>Dr(a). {{ $diagnostic->doctor->name }}</strong></td>
            </tr>
            @if($diagnostic->especialidad)
            <tr>
                <td>Especialidad:</td>
                <td>{{ $diagnostic->especialidad }}</td>
            </tr>
            @endif
            <tr>
                <td>ID de Consulta:</td>
                <td>#{{ $diagnostic->id }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">🔍 Diagnóstico Clínico</div>
        <div class="content-box">{{ $diagnostic->diagnostico }}</div>
    </div>

    @if($diagnostic->medicamentos)
    <div class="section">
        <div class="section-title">💊 Medicamentos Recetados</div>
        <div class="content-box">{{ $diagnostic->medicamentos }}</div>
    </div>
    @endif

    @if($diagnostic->procedimientos)
    <div class="section">
        <div class="section-title">🔬 Procedimientos Realizados</div>
        <div class="content-box">{{ $diagnostic->procedimientos }}</div>
    </div>
    @endif

    @if($diagnostic->remisiones)
    <div class="section">
        <div class="section-title">🏥 Remisiones a Especialistas</div>
        <div class="content-box">{{ $diagnostic->remisiones }}</div>
    </div>
    @endif

    @if($diagnostic->observaciones)
    <div class="section">
        <div class="section-title">📝 Observaciones y Recomendaciones</div>
        <div class="content-box">{{ $diagnostic->observaciones }}</div>
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <p><strong>Dr(a). {{ $diagnostic->doctor->name }}</strong></p>
            <p style="font-size: 9pt; color: #666;">{{ $diagnostic->especialidad ?? 'Médico General' }}</p>
            <p style="font-size: 9pt; color: #666;">Fecha: {{ $diagnostic->fecha_consulta->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="footer">
        <p><strong>MediCitas</strong> - Sistema de Gestión Médica</p>
        <p>Documento generado el {{ date('d/m/Y H:i:s') }}</p>
        <p>Este documento es una copia fiel del diagnóstico registrado en nuestro sistema</p>
    </div>
</body>
</html>
