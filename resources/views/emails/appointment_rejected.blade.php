<!-- Correo: Cita rechazada -->
<!-- Comentarios: Esta vista informa el rechazo y sugiere reprogramar. -->
@php($a = $appointment)
<p>Hola {{ $a->patient_name }},</p>
<p>Lamentamos informarte que tu cita con <strong>{{ $a->doctor->name }}</strong> ha sido <strong>rechazada</strong>.</p>
<ul>
  <li>Fecha y hora solicitada: {{ \Carbon\Carbon::parse($a->start_at)->format('d/m/Y H:i') }}</li>
  <li>Estado: {{ $a->status }}</li>
</ul>
<p>Te invitamos a reprogramar seleccionando otro espacio disponible en el calendario.</p>
<p>Gracias,</p>
<p>{{ config('app.name') }}</p>
