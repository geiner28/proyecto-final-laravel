<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cita Confirmada</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5;">
    <div style="background-color: #ffffff; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 20px; border-radius: 10px 10px 0 0; text-align: center; margin: -30px -30px 30px -30px;">
            <h1 style="margin: 0; font-size: 24px;">🏥 MediCitas</h1>
            <p style="margin: 5px 0 0 0;">Sistema de Gestión de Citas Médicas</p>
        </div>

        <div style="font-size: 64px; text-align: center; margin: 20px 0;">✅</div>
        
        <h2 style="color: #065f46; text-align: center;">¡Su Cita ha sido Confirmada!</h2>
        
        <p>Estimado(a) <strong>{{ $appointment->patient_name }}</strong>,</p>
        
        <p>Nos complace informarle que su cita médica ha sido <strong>confirmada</strong> exitosamente.</p>
        
        <div style="text-align: center;">
            <span style="display: inline-block; background-color: #10b981; color: white; padding: 8px 16px; border-radius: 20px; font-weight: bold; font-size: 14px; margin: 15px 0;">✓ CONFIRMADA</span>
        </div>

        <div style="background-color: #f0fdf4; border-left: 4px solid #10b981; padding: 15px; margin: 20px 0; border-radius: 5px;">
            <h3 style="margin-top: 0; color: #065f46;">📋 Detalles de su Cita</h3>
            
            <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                <div style="font-weight: bold; color: #6b7280; min-width: 120px;">👨‍⚕️ Médico:</div>
                <div style="color: #111827;">{{ $appointment->doctor->name }}</div>
            </div>
            
            <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                <div style="font-weight: bold; color: #6b7280; min-width: 120px;">🏥 Especialidad:</div>
                <div style="color: #111827;">{{ $appointment->doctor->specialty }}</div>
            </div>
            
            <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                <div style="font-weight: bold; color: #6b7280; min-width: 120px;">📅 Fecha:</div>
                <div style="color: #111827;">{{ \Carbon\Carbon::parse($appointment->start_at)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</div>
            </div>
            
            <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                <div style="font-weight: bold; color: #6b7280; min-width: 120px;">🕐 Hora:</div>
                <div style="color: #111827;">{{ \Carbon\Carbon::parse($appointment->start_at)->format('h:i A') }}</div>
            </div>
            
            <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                <div style="font-weight: bold; color: #6b7280; min-width: 120px;">⏱️ Duración:</div>
                <div style="color: #111827;">{{ env('APPOINTMENT_DURATION_MINUTES', 20) }} minutos</div>
            </div>
            
            @if($appointment->notes)
            <div style="display: flex; padding: 10px 0;">
                <div style="font-weight: bold; color: #6b7280; min-width: 120px;">📝 Notas:</div>
                <div style="color: #111827;">{{ $appointment->notes }}</div>
            </div>
            @endif
        </div>

        <div style="background-color: #dbeafe; border-left: 4px solid #3b82f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 0;"><strong>📌 Recordatorios Importantes:</strong></p>
            <ul style="margin: 10px 0;">
                <li>Por favor llegue 10 minutos antes de su cita</li>
                <li>Traiga su documento de identidad</li>
                <li>Si necesita cancelar o reprogramar, contáctenos con anticipación</li>
            </ul>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/consultar-cita') }}" style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 20px 0;">🔍 Ver Detalles de mi Cita</a>
        </div>

        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #e5e7eb; color: #6b7280; font-size: 14px;">
            <p><strong>MediCitas - Sistema de Gestión de Citas Médicas</strong></p>
            <p>¡Esperamos verle pronto!</p>
            <p style="margin-top: 10px;">© {{ date('Y') }} MediCitas. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
