<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Mail\AppointmentCreatedMail;
use App\Mail\AppointmentCancelledMail;
use App\Mail\AppointmentRescheduledMail;
use Carbon\Carbon;

class PublicAppointmentController extends Controller
{
    /**
     * Crea una cita en estado pendiente: valida colisión y disponibilidad, y envía email.
     */
    public function store(Request $request)
    {
        // Valida datos básicos del paciente y parámetros
        $data = $request->validate([
            'doctor' => ['required', 'string'],
            'start' => ['required', 'date'],
            'patient_name' => ['required', 'string', 'max:255'],
            'patient_email' => ['required', 'email'],
            'cedula_paciente' => ['required', 'string', 'max:20'],
            'telefono_paciente' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
        ]);

        $doctor = Doctor::where('slug', $data['doctor'])->firstOrFail();
        $duration = (int) env('APPOINTMENT_DURATION_MINUTES', 20);
        $start = Carbon::parse($data['start']);
        $end = (clone $start)->addMinutes($duration);

        // Valida que el horario esté dentro de alguna disponibilidad del médico
        $weekday = (int) $start->dayOfWeekIso - 1; // lunes=0 ... domingo=6
        $enDisponibilidad = $doctor->availabilities()
            ->where('weekday', $weekday)
            ->where('start_time', '<=', $start->format('H:i:s'))
            ->where('end_time', '>=', $end->format('H:i:s'))
            ->exists();
        if (!$enDisponibilidad) {
            return back()->withErrors(['start' => 'El horario seleccionado no está dentro de la disponibilidad del médico.']);
        }

        // Validar que la cita no sea en el pasado
        if ($start->isPast()) {
            return back()->withErrors(['start' => 'No se pueden agendar citas en fechas u horas que ya han pasado.']);
        }

        // Valida colisiones: mismo médico no puede tener dos citas pendientes o confirmadas en misma hora
        $collision = $doctor->appointments()
            ->where('start_at', $start)
            ->whereIn('status', ['pendiente', 'confirmada'])
            ->exists();
        if ($collision) {
            return back()->withErrors(['start' => 'Ese horario ya está ocupado para este médico. Por favor selecciona otro horario.']);
        }

        // VALIDACIÓN CRÍTICA: El mismo paciente (cédula) no puede tener citas el mismo día/hora
        // aunque sea con diferente médico - regla de negocio para empresa de salud
        $pacienteConCitaDuplicada = Appointment::where('cedula_paciente', $data['cedula_paciente'])
            ->where('start_at', $start)
            ->whereIn('status', ['pendiente', 'confirmada'])
            ->exists();
        if ($pacienteConCitaDuplicada) {
            return back()->withErrors([
                'cedula_paciente' => 'Ya tienes una cita agendada para este mismo día y hora. No puedes tener dos citas simultáneas.'
            ]);
        }

        // Crea cita en pendiente
        $appointment = Appointment::create([
            'doctor_id' => $doctor->id,
            'slug' => Str::uuid()->toString(),
            'patient_name' => $data['patient_name'],
            'patient_email' => $data['patient_email'],
            'cedula_paciente' => $data['cedula_paciente'],
            'telefono_paciente' => $data['telefono_paciente'] ?? null,
            'start_at' => $start,
            'end_at' => $end,
            'status' => 'pendiente',
            'notes' => $data['notes'] ?? null,
        ]);

        // Envía correo de cita reservada
        Mail::to($appointment->patient_email)->send(new AppointmentCreatedMail($appointment));

        return redirect()->route('public.doctor', $doctor)->with('success', 'Cita creada en estado pendiente.');
    }

    /**
     * Muestra el formulario para consultar citas por cédula
     */
    public function consultaForm()
    {
        return Inertia::render('ConsultarCita');
    }

    /**
     * Busca y muestra las citas de un paciente por su cédula
     */
    public function consultar(Request $request)
    {
        $data = $request->validate([
            'cedula' => ['required', 'string'],
        ]);

        $appointments = Appointment::where('cedula_paciente', $data['cedula'])
            ->with('doctor')
            ->orderBy('start_at', 'desc')
            ->get();

        return Inertia::render('ConsultarCita', [
            'appointments' => $appointments,
            'cedula' => $data['cedula'],
        ]);
    }

    /**
     * Cancela una cita existente (solo si está pendiente o confirmada y no ha pasado)
     */
    public function cancel(Request $request, Appointment $appointment)
    {
        // Validar que la cita puede ser cancelada
        if (!in_array($appointment->status, ['pendiente', 'confirmada'])) {
            return back()->withErrors(['appointment' => 'Esta cita no se puede cancelar porque su estado es: ' . $appointment->status]);
        }

        if (Carbon::parse($appointment->start_at)->isPast()) {
            return back()->withErrors(['appointment' => 'No se pueden cancelar citas que ya han pasado.']);
        }

        // Actualizar estado
        $appointment->update(['status' => 'cancelada']);

        // Enviar email de notificación
        Mail::to($appointment->patient_email)->send(new AppointmentCancelledMail($appointment));

        return back()->with('success', 'Cita cancelada exitosamente.');
    }

    /**
     * Muestra el formulario para reagendar (devuelve disponibilidad del doctor)
     */
    public function rescheduleForm(Appointment $appointment)
    {
        // Validar que la cita puede ser reagendada
        if (!in_array($appointment->status, ['pendiente', 'confirmada'])) {
            return back()->withErrors(['appointment' => 'Esta cita no se puede reagendar.']);
        }

        if (Carbon::parse($appointment->start_at)->isPast()) {
            return back()->withErrors(['appointment' => 'No se pueden reagendar citas que ya han pasado.']);
        }

        $doctor = $appointment->doctor;
        $duration = (int) env('APPOINTMENT_DURATION_MINUTES', 20);

        // Calcular disponibilidad de los próximos 2 meses en formato de slots
        $startDate = Carbon::now()->startOfMonth();
        $endDate = (clone $startDate)->addMonths(2)->endOfMonth();
        $availability = $this->calcularDisponibilidadRango($doctor, $appointment, $startDate, $endDate, $duration);

        // Devolver como JSON para AJAX
        return response()->json([
            'success' => true,
            'availability' => $availability,
            'duration' => $duration
        ]);
    }

    /**
     * Reagenda una cita a un nuevo horario
     */
    public function reschedule(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'new_start' => ['required', 'date'],
        ]);

        // Validar que la cita puede ser reagendada
        if (!in_array($appointment->status, ['pendiente', 'confirmada'])) {
            return back()->withErrors(['appointment' => 'Esta cita no se puede reagendar.']);
        }

        if (Carbon::parse($appointment->start_at)->isPast()) {
            return back()->withErrors(['appointment' => 'No se pueden reagendar citas que ya han pasado.']);
        }

        $doctor = $appointment->doctor;
        $duration = (int) env('APPOINTMENT_DURATION_MINUTES', 20);
        $newStart = Carbon::parse($data['new_start']);
        $newEnd = (clone $newStart)->addMinutes($duration);

        // Validar que el nuevo horario está en el futuro
        if ($newStart->isPast()) {
            return back()->withErrors(['new_start' => 'No se pueden agendar citas en fechas u horas que ya han pasado.']);
        }

        // Validar que el nuevo horario esté dentro de la disponibilidad del médico
        $weekday = (int) $newStart->dayOfWeekIso - 1;
        $enDisponibilidad = $doctor->availabilities()
            ->where('weekday', $weekday)
            ->where('start_time', '<=', $newStart->format('H:i:s'))
            ->where('end_time', '>=', $newEnd->format('H:i:s'))
            ->exists();

        if (!$enDisponibilidad) {
            return back()->withErrors(['new_start' => 'El nuevo horario no está dentro de la disponibilidad del médico.']);
        }

        // Validar colisiones (excluyendo la cita actual)
        $collision = $doctor->appointments()
            ->where('id', '!=', $appointment->id)
            ->where('start_at', $newStart)
            ->whereIn('status', ['pendiente', 'confirmada'])
            ->exists();

        if ($collision) {
            return back()->withErrors(['new_start' => 'Ese horario ya está ocupado. Por favor selecciona otro.']);
        }

        // Validar que el paciente no tenga otra cita en ese horario (excluyendo la actual)
        $pacienteConCitaDuplicada = Appointment::where('cedula_paciente', $appointment->cedula_paciente)
            ->where('id', '!=', $appointment->id)
            ->where('start_at', $newStart)
            ->whereIn('status', ['pendiente', 'confirmada'])
            ->exists();

        if ($pacienteConCitaDuplicada) {
            return back()->withErrors(['new_start' => 'Ya tienes otra cita agendada para este día y hora.']);
        }

        // Guardar horarios anteriores para el email
        $oldStart = $appointment->start_at;

        // Actualizar la cita
        $appointment->update([
            'start_at' => $newStart,
            'end_at' => $newEnd,
        ]);

        // Enviar email de notificación
        Mail::to($appointment->patient_email)->send(new AppointmentRescheduledMail($appointment, $oldStart));

        return back()->with('success', 'Cita reagendada exitosamente.');
    }

    /**
     * Calcula disponibilidad en un rango de fechas para reagendar (excluye la cita actual).
     */
    protected function calcularDisponibilidadRango(Doctor $doctor, Appointment $currentAppointment, Carbon $startDate, Carbon $endDate, int $duration): array
    {
        $slots = [];
        
        // Obtener todas las citas en el rango (excepto la cita actual que se está reagendando)
        $appointments = $doctor->appointments()
            ->where('id', '!=', $currentAppointment->id)
            ->whereBetween('start_at', [$startDate, $endDate])
            ->whereIn('status', ['pendiente', 'confirmada'])
            ->get()
            ->keyBy(function ($a) {
                return $a->start_at->toIso8601String();
            });

        // Iterar cada día del rango
        $currentDay = $startDate->copy();
        while ($currentDay->lte($endDate)) {
            // Excluir sábados y domingos
            if ($currentDay->dayOfWeek === 0 || $currentDay->dayOfWeek === 6) {
                $currentDay->addDay();
                continue;
            }
            
            // Excluir fechas pasadas (comparar solo fechas, no horas)
            if ($currentDay->toDateString() < Carbon::now()->toDateString()) {
                $currentDay->addDay();
                continue;
            }

            // Buscar disponibilidades del médico para este día de la semana
            $weekday = $currentDay->dayOfWeek === 0 ? 6 : $currentDay->dayOfWeek - 1;
            
            foreach ($doctor->availabilities->where('weekday', $weekday) as $avail) {
                $start = Carbon::parse($currentDay->toDateString() . ' ' . $avail->start_time);
                $end = Carbon::parse($currentDay->toDateString() . ' ' . $avail->end_time);
                
                // Si es hoy, ajustar inicio
                if ($currentDay->isToday() && $start->isPast()) {
                    $start = Carbon::now()->minute(0)->second(0);
                    $minutes = $start->minute;
                    $nextSlot = (int)(ceil($minutes / $duration) * $duration);
                    $start->minute($nextSlot);
                }

                while ($start->lt($end)) {
                    $slotStart = $start->copy();
                    $slotEnd = $start->copy()->addMinutes($duration);
                    if ($slotEnd->gt($end)) {
                        break;
                    }
                    
                    if ($slotStart->isFuture() || $slotStart->isCurrentMinute()) {
                        $key = $slotStart->toIso8601String();
                        if (!$appointments->has($key)) {
                            $slots[] = [
                                'start' => $slotStart->toIso8601String(),
                                'end' => $slotEnd->toIso8601String(),
                                'doctor' => $doctor->slug,
                            ];
                        }
                    }
                    $start->addMinutes($duration);
                }
            }
            
            $currentDay->addDay();
        }
        
        usort($slots, fn($a, $b) => strcmp($a['start'], $b['start']));
        return $slots;
    }
}
