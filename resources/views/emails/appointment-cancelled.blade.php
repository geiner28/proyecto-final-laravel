<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cita Cancelada</title>
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
            background: linear-gradient(135deg, #ef4444 0%, #ec4899 100%);
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
            border-left: 4px solid #ef4444;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
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
        <h1>❌ Cita Cancelada</h1>
    </div>
    <div class="content">
        <p>Hola <strong>{{ $patientName }}</strong>,</p>
        
        <p>Te confirmamos que tu cita ha sido <strong>cancelada exitosamente</strong>.</p>

        <div class="info-box">
            <h3 style="margin-top: 0; color: #ef4444;">Detalles de la cita cancelada:</h3>
            
            <div class="info-item">
                <span class="label">👨‍⚕️ Doctor:</span><br>
                {{ $doctorName }}<br>
                <small style="color: #6b7280;">{{ $doctorSpecialty }}</small>
            </div>

            <div class="info-item">
                <span class="label">📅 Fecha:</span><br>
                {{ \Carbon\Carbon::parse($startAt)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
            </div>

            <div class="info-item">
                <span class="label">🕐 Hora:</span><br>
                {{ \Carbon\Carbon::parse($startAt)->format('h:i A') }}
            </div>
        </div>

        <p>Si deseas agendar una nueva cita, puedes hacerlo visitando nuestra plataforma.</p>

        <div class="footer">
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            <p>&copy; {{ date('Y') }} Sistema de Citas Médicas</p>
        </div>
    </div>
</body>
</html>
