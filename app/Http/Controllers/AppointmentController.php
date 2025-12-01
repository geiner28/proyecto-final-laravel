<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Mail\AppointmentAcceptedMail;
use App\Mail\AppointmentRejectedMail;
use App\Mail\AppointmentCancelledMail;
use App\Mail\AppointmentRescheduledMail;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Listado de citas con filtros por estado, médico y búsqueda
        $query = Appointment::query()->with(['doctor', 'diagnostic']);

        // Filtro por búsqueda (nombre o cédula)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('patient_name', 'ILIKE', "%{$search}%")
                  ->orWhere('cedula_paciente', 'ILIKE', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por doctor
        if ($request->filled('doctor')) {
            $query->where('doctor_id', $request->doctor);
        }

        $appointments = $query->latest('start_at')->paginate(15)->withQueryString();
        $doctors = Doctor::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Appointments/Index', [
            'appointments' => $appointments,
            'doctors' => $doctors,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
                'doctor' => $request->doctor,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Formulario de creación (panel)
        $doctors = Doctor::orderBy('name')->get();
        return Inertia::render('Appointments/Create', [
            'doctors' => $doctors,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Creación de cita desde panel (opcional)
        $data = $request->validate([
            'doctor_id' => ['required','exists:doctors,id'],
            'patient_name' => ['required','string','max:255'],
            'patient_email' => ['required','email'],
            'cedula_paciente' => ['required','string','max:20'],
            'telefono_paciente' => ['nullable','string','max:20'],
            'start_at' => ['required','date'],
            'notes' => ['nullable','string'],
        ]);
        $duration = (int) env('APPOINTMENT_DURATION_MINUTES', 20);
        $start = Carbon::parse($data['start_at']);
        $end = (clone $start)->addMinutes($duration);
        
        // Validar que la cita no sea en el pasado
        if ($start->isPast()) {
            return back()->withErrors(['start_at' => 'No se pueden agendar citas en fechas u horas que ya han pasado.']);
        }
        
        // Valida colisión con mismo médico
        $collision = Appointment::where('doctor_id', $data['doctor_id'])
            ->where('start_at', $start)
            ->whereIn('status', ['pendiente', 'confirmada'])
            ->exists();
        if ($collision) {
            return back()->withErrors(['start_at' => 'Ese horario ya está ocupado para este médico. Por favor selecciona otro horario.']);
        }
        
        // VALIDACIÓN CRÍTICA: El mismo paciente (cédula) no puede tener citas el mismo día/hora
        $pacienteConCitaDuplicada = Appointment::where('cedula_paciente', $data['cedula_paciente'])
            ->where('start_at', $start)
            ->whereIn('status', ['pendiente', 'confirmada'])
            ->exists();
        if ($pacienteConCitaDuplicada) {
            return back()->withErrors([
                'cedula_paciente' => 'Este paciente ya tiene una cita agendada para este mismo día y hora. No puede tener dos citas simultáneas.'
            ]);
        }
        
        Appointment::create([
            'doctor_id' => $data['doctor_id'],
            'slug' => \Illuminate\Support\Str::uuid()->toString(),
            'patient_name' => $data['patient_name'],
            'patient_email' => $data['patient_email'],
            'cedula_paciente' => $data['cedula_paciente'],
            'telefono_paciente' => $data['telefono_paciente'] ?? null,
            'start_at' => $start,
            'end_at' => $end,
            'status' => 'pendiente',
            'notes' => $data['notes'] ?? null,
        ]);
        return redirect()->route('panel.appointments.index')->with('success', 'Cita creada en pendiente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        // Muestra detalle de la cita
        return Inertia::render('Appointments/Show', [
            'appointment' => $appointment->load('doctor'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        // Formulario de edición
        $doctors = Doctor::orderBy('name')->get();
        return Inertia::render('Appointments/Edit', [
            'appointment' => $appointment,
            'doctors' => $doctors,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        // Actualiza datos básicos de la cita (no estado)
        $data = $request->validate([
            'patient_name' => ['required','string','max:255'],
            'patient_email' => ['required','email'],
            'notes' => ['nullable','string'],
        ]);
        $appointment->update($data);
        return redirect()->route('panel.appointments.show', $appointment)->with('success', 'Cita actualizada');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        // Cancela/elimina la cita
        $appointment->delete();
        return redirect()->route('panel.appointments.index')->with('success', 'Cita eliminada');
    }

    /**
     * Resumen de pendientes y próximas confirmadas.
     */
    public function home(Request $request)
    {
        $user = $request->user();
        $query = Appointment::query()->with(['doctor', 'diagnostic']);
        
        // Si el usuario es médico, filtrar solo sus citas
        if ($user->isDoctor() && $user->doctor) {
            $query->where('doctor_id', $user->doctor->id);
        } else {
            // Si es admin/paciente, puede filtrar por médico desde la URL
            $doctorSlug = $request->get('doctor');
            if ($doctorSlug) {
                $doctor = Doctor::where('slug', $doctorSlug)->first();
                if ($doctor) {
                    $query->where('doctor_id', $doctor->id);
                }
            }
        }
        
        $pending = (clone $query)->where('status', 'pendiente')->orderBy('start_at')->limit(20)->get();
        $upcoming = (clone $query)->where('status', 'confirmada')->where('start_at', '>=', Carbon::now())->orderBy('start_at')->limit(20)->get();
        $doctors = Doctor::orderBy('name')->get();
        
        return Inertia::render('Admin/Home', [
            'pending' => $pending,
            'upcoming' => $upcoming,
            'doctors' => $doctors,
            'currentDoctor' => $user->isDoctor() ? $user->doctor : null,
        ]);
    }

    /**
     * Calendario semanal del panel: muestra citas pendientes y confirmadas.
     */
    public function calendar(Request $request)
    {
        $user = $request->user();
        
        // Si el usuario es médico, usar su doctor asociado
        if ($user->isDoctor() && $user->doctor) {
            $doctor = $user->doctor;
        } else {
            // Si es admin, requerir el parámetro doctor
            $doctorSlug = $request->get('doctor');
            if (!$doctorSlug) {
                // Si no hay doctor especificado, redirigir a home
                return redirect()->route('panel.home');
            }
            $doctor = Doctor::where('slug', $doctorSlug)->firstOrFail();
        }
        
        $startOfWeek = Carbon::parse($request->get('week', Carbon::now()))->startOfWeek(Carbon::MONDAY);
        $weekAppointments = $doctor->appointments()
            ->whereBetween('start_at', [$startOfWeek, (clone $startOfWeek)->addDays(7)])
            ->orderBy('start_at')
            ->get();
        return Inertia::render('Admin/Calendar', [
            'doctor' => $doctor,
            'weekStart' => $startOfWeek->toDateString(),
            'appointments' => $weekAppointments,
        ]);
    }

    /**
     * Acepta una cita pendiente y envía email.
     */
    public function accept(Appointment $appointment)
    {
        if ($appointment->status !== 'pendiente') {
            return back()->withErrors(['status' => 'Solo se pueden aceptar citas pendientes.']);
        }
        $appointment->update(['status' => 'confirmada']);
        Mail::to($appointment->patient_email)->send(new AppointmentAcceptedMail($appointment));
        return back()->with('success', 'Cita aceptada');
    }

    /**
     * Rechaza una cita pendiente y envía email.
     */
    public function reject(Appointment $appointment)
    {
        if ($appointment->status !== 'pendiente') {
            return back()->withErrors(['status' => 'Solo se pueden rechazar citas pendientes.']);
        }
        $appointment->update(['status' => 'rechazada']);
        Mail::to($appointment->patient_email)->send(new AppointmentRejectedMail($appointment));
        return back()->with('success', 'Cita rechazada');
    }

    /**
     * Cancela una cita desde el panel de admin
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
     * Retorna disponibilidad del doctor para reagendar (admin)
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

        return response()->json([
            'success' => true,
            'availability' => $availability,
            'duration' => $duration
        ]);
    }

    /**
     * Reagenda una cita desde el panel de admin
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
            return back()->withErrors(['new_start' => 'El paciente ya tiene otra cita agendada para este día y hora.']);
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
