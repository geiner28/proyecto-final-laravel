<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cita Registrada</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            text-align: center;
            margin: -30px -30px 30px -30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .status-badge {
            display: inline-block;
            background-color: #fbbf24;
            color: #78350f;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            margin: 15px 0;
        }
        .info-box {
            background-color: #f0f9ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .info-box h3 {
            margin-top: 0;
            color: #1e40af;
        }
        .detail-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-label {
            font-weight: bold;
            color: #6b7280;
            min-width: 120px;
        }
        .detail-value {
            color: #111827;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 MediCitas</h1>
            <p style="margin: 5px 0 0 0;">Sistema de Gestión de Citas Médicas</p>
        </div>

        <h2 style="color: #1f2937;">¡Cita Registrada Exitosamente!</h2>
        
        <p>Estimado(a) <strong>{{ $appointment->patient_name }}</strong>,</p>
        
        <p>Su cita médica ha sido registrada en nuestro sistema y se encuentra en estado:</p>
        
        <div style="text-align: center;">
            <span class="status-badge">⏳ PENDIENTE DE CONFIRMACIÓN</span>
        </div>

        <div class="info-box">
            <h3>📋 Detalles de su Cita</h3>
            
            <div class="detail-row">
                <div class="detail-label">👨‍⚕️ Médico:</div>
                <div class="detail-value">{{ $appointment->doctor->name }}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">🏥 Especialidad:</div>
                <div class="detail-value">{{ $appointment->doctor->specialty }}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">📅 Fecha:</div>
                <div class="detail-value">{{ \Carbon\Carbon::parse($appointment->start_at)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">🕐 Hora:</div>
                <div class="detail-value">{{ \Carbon\Carbon::parse($appointment->start_at)->format('h:i A') }}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">⏱️ Duración:</div>
                <div class="detail-value">{{ env('APPOINTMENT_DURATION_MINUTES', 20) }} minutos</div>
            </div>
            
            @if($appointment->notes)
            <div class="detail-row">
                <div class="detail-label">📝 Notas:</div>
                <div class="detail-value">{{ $appointment->notes }}</div>
            </div>
            @endif
        </div>

        <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 0;"><strong>⚠️ Importante:</strong></p>
            <p style="margin: 5px 0 0 0;">Su cita será revisada por nuestro equipo médico. Recibirá un correo de confirmación o rechazo en las próximas horas.</p>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/consultar-cita') }}" class="btn">🔍 Consultar Estado de mi Cita</a>
        </div>

        <p style="color: #6b7280; font-size: 14px;">
            Para consultar el estado de su cita en cualquier momento, visite nuestra página e ingrese su número de cédula: <strong>{{ $appointment->cedula_paciente }}</strong>
        </p>

        <div class="footer">
            <p><strong>MediCitas - Sistema de Gestión de Citas Médicas</strong></p>
            <p>Este es un correo automático, por favor no responda a este mensaje.</p>
            <p style="margin-top: 10px;">© {{ date('Y') }} MediCitas. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
