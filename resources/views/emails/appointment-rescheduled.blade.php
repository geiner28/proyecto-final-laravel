<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cita Reagendada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            color: white;
            padding: 30px;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .info-box {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .old-date {
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin-bottom: 20px;
            background: #fef2f2;
        }
        .new-date {
            border-left: 4px solid #10b981;
            padding: 15px;
            background: #f0fdf4;
        }
        .info-item {
            margin: 10px 0;
        }
        .label {
            font-weight: bold;
            color: #6b7280;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📅 Cita Reagendada</h1>
    </div>
    <div class="content">
        <p>Hola <strong>{{ $patientName }}</strong>,</p>
        
        <p>Te confirmamos que tu cita ha sido <strong>reagendada exitosamente</strong>.</p>

        <div class="info-box">
            <h3 style="margin-top: 0; color: #f59e0b;">👨‍⚕️ Doctor</h3>
            <p style="margin: 5px 0;">
                <strong>{{ $doctorName }}</strong><br>
                <small style="color: #6b7280;">{{ $doctorSpecialty }}</small>
            </p>

            <div class="old-date">
                <h4 style="margin-top: 0; color: #ef4444;">❌ Horario anterior (cancelado):</h4>
                <div class="info-item">
                    <span class="label">📅 Fecha:</span><br>
                    {{ \Carbon\Carbon::parse($oldStart)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                </div>
                <div class="info-item">
                    <span class="label">🕐 Hora:</span><br>
                    {{ \Carbon\Carbon::parse($oldStart)->format('h:i A') }}
                </div>
            </div>

            <div class="new-date">
                <h4 style="margin-top: 0; color: #10b981;">✅ Nuevo horario confirmado:</h4>
                <div class="info-item">
                    <span class="label">📅 Fecha:</span><br>
                    {{ \Carbon\Carbon::parse($newStart)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                </div>
                <div class="info-item">
                    <span class="label">🕐 Hora:</span><br>
                    {{ \Carbon\Carbon::parse($newStart)->format('h:i A') }}
                </div>
            </div>
        </div>

        <p><strong>⚠️ Importante:</strong> Por favor llega 10 minutos antes de tu cita.</p>

        <p>Si necesitas hacer algún cambio adicional, puedes consultar tu cita en nuestra plataforma.</p>

        <div class="footer">
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            <p>&copy; {{ date('Y') }} Sistema de Citas Médicas</p>
        </div>
    </div>
</body>
</html>
