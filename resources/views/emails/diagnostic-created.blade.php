<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico Médico</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f7f9;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 3px solid #3b82f6;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #3b82f6;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            color: #6b7280;
            margin: 5px 0 0 0;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            color: #1f2937;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e5e7eb;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 140px 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }
        .info-label {
            font-weight: 600;
            color: #4b5563;
        }
        .info-value {
            color: #1f2937;
        }
        .diagnostic-box {
            background-color: #f0f9ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .footer a {
            color: #3b82f6;
            text-decoration: none;
        }
        .alert {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .alert-icon {
            font-size: 20px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 MediCitas</h1>
            <p>Diagnóstico Médico Disponible</p>
        </div>

        <p>Estimado/a <strong>{{ $diagnostic->patient_name }}</strong>,</p>
        <p>Su diagnóstico médico ha sido registrado exitosamente en nuestro sistema. A continuación encontrará los detalles:</p>

        <div class="section">
            <div class="section-title">📋 Información de la Consulta</div>
            <div class="info-grid">
                <div class="info-label">Fecha:</div>
                <div class="info-value">{{ $diagnostic->fecha_consulta->format('d/m/Y') }}</div>
                
                <div class="info-label">Médico:</div>
                <div class="info-value">Dr(a). {{ $diagnostic->doctor->name }}</div>
                
                <div class="info-label">Especialidad:</div>
                <div class="info-value">{{ $diagnostic->especialidad ?? 'General' }}</div>
                
                <div class="info-label">Cédula Paciente:</div>
                <div class="info-value">{{ $diagnostic->cedula_paciente }}</div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">🩺 Diagnóstico</div>
            <div class="diagnostic-box">
                {{ $diagnostic->diagnostico }}
            </div>
        </div>

        @if($diagnostic->medicamentos)
        <div class="section">
            <div class="section-title">💊 Medicamentos Recetados</div>
            <div class="diagnostic-box">
                {!! nl2br(e($diagnostic->medicamentos)) !!}
            </div>
        </div>
        @endif

        @if($diagnostic->procedimientos)
        <div class="section">
            <div class="section-title">🔬 Procedimientos Realizados</div>
            <div class="diagnostic-box">
                {!! nl2br(e($diagnostic->procedimientos)) !!}
            </div>
        </div>
        @endif

        @if($diagnostic->remisiones)
        <div class="section">
            <div class="section-title">🏥 Remisiones</div>
            <div class="diagnostic-box">
                {!! nl2br(e($diagnostic->remisiones)) !!}
            </div>
        </div>
        @endif

        @if($diagnostic->observaciones)
        <div class="section">
            <div class="section-title">📝 Observaciones Adicionales</div>
            <div class="diagnostic-box">
                {!! nl2br(e($diagnostic->observaciones)) !!}
            </div>
        </div>
        @endif

        <div class="alert">
            <span class="alert-icon">ℹ️</span>
            <strong>Consulta tu Historia Clínica:</strong> Puedes consultar todos tus diagnósticos en cualquier momento ingresando tu cédula en 
            <a href="{{ url('/consultar-diagnostico') }}">{{ url('/consultar-diagnostico') }}</a>
        </div>

        <div class="footer">
            <p><strong>MediCitas - Sistema de Gestión Médica</strong></p>
            <p>Este es un correo automático, por favor no responder directamente.</p>
            <p>Si tiene alguna duda sobre su diagnóstico, contacte directamente con su médico tratante.</p>
        </div>
    </div>
</body>
</html>
