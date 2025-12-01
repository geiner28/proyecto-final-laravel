<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentAcceptedMail;
use App\Mail\AppointmentRejectedMail;
use App\Mail\AppointmentCancelledMail;
use App\Mail\AppointmentRescheduledMail;
use Carbon\Carbon;

class DoctorAppointmentController extends Controller
{
    /**
     * Dashboard principal del médico con calendario semanal
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $doctor = Doctor::where('user_id', $user->id)->firstOrFail();
        
        // Fecha de inicio de la semana para el calendario (lunes)
        $weekStart = $request->filled('week_start') 
            ? Carbon::parse($request->week_start)->startOfWeek() 
            : Carbon::now()->startOfWeek();
        
        $weekEnd = (clone $weekStart)->endOfWeek();
        
        // Obtener TODAS las citas del doctor para estadísticas
        $allAppointments = Appointment::where('doctor_id', $doctor->id)->get();
        
        // Estadísticas (siempre sobre todas las citas)
        $today = Carbon::today();
        $stats = [
            'today' => $allAppointments->filter(function ($apt) use ($today) {
                return Carbon::parse($apt->start_at)->isSameDay($today);
            })->count(),
            'week' => $allAppointments->filter(function ($apt) use ($weekStart, $weekEnd) {
                $date = Carbon::parse($apt->start_at);
                return $date->between($weekStart, $weekEnd);
            })->count(),
            'pending' => $allAppointments->where('status', 'pendiente')->count(),
            'confirmed' => $allAppointments->where('status', 'confirmada')->count(),
            'completed' => $allAppointments->where('status', 'completada')->count(),
            'total' => $allAppointments->count(),
        ];
        
        // FILTROS: Query independiente del calendario
        // Los filtros buscan en TODAS las citas, no solo de la semana
        $filteredQuery = Appointment::where('doctor_id', $doctor->id)
            ->with(['doctor', 'diagnostic']);
        
        // Variable para saber si hay filtros activos
        $hasFilters = false;
        
        // Filtro por búsqueda (nombre, email o cédula)
        if ($request->filled('search')) {
            $hasFilters = true;
            $search = $request->search;
            $filteredQuery->where(function ($q) use ($search) {
                $q->where('patient_name', 'ILIKE', "%{$search}%")
                  ->orWhere('patient_email', 'ILIKE', "%{$search}%")
                  ->orWhere('cedula_paciente', 'ILIKE', "%{$search}%");
            });
        }
        
        // Filtro por estado
        if ($request->filled('status')) {
            $hasFilters = true;
            $filteredQuery->where('status', $request->status);
        }
        
        // Filtro por fecha específica
        if ($request->filled('date')) {
            $hasFilters = true;
            $date = Carbon::parse($request->date);
            $filteredQuery->whereDate('start_at', $date);
        }
        
        // Ordenar por fecha
        $filteredQuery->orderBy('start_at', 'asc');
        
        // Obtener citas filtradas (todas las que coincidan con los filtros)
        $filteredAppointments = $filteredQuery->get();
        
        // CALENDARIO: Siempre muestra la semana seleccionada
        // Obtener solo las citas de la semana actual para el calendario
        $weekAppointments = $allAppointments->filter(function ($apt) use ($weekStart, $weekEnd) {
            $date = Carbon::parse($apt->start_at);
            return $date->between($weekStart, $weekEnd);
        });
        
        // Calcular disponibilidad semanal con citas de la semana
        $weekData = $this->calculateWeekAvailability($doctor, $weekStart, $weekAppointments);
        
        return Inertia::render('Doctor/Dashboard', [
            'appointments' => $filteredAppointments, // Citas filtradas para la lista
            'doctor' => $doctor,
            'weekData' => $weekData, // Calendario siempre muestra la semana actual
            'weekStart' => $weekStart->toDateString(),
            'weekEnd' => $weekEnd->toDateString(),
            'stats' => $stats,
            'hasFilters' => $hasFilters, // Indica si hay filtros activos
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
                'date' => $request->date,
            ],
        ]);
    }
    
    /**
     * Calcular disponibilidad semanal con citas del doctor
     */
    protected function calculateWeekAvailability(Doctor $doctor, Carbon $weekStart, $appointments)
    {
        $weekData = [];
        $duration = (int) env('APPOINTMENT_DURATION_MINUTES', 20);
        
        for ($i = 0; $i < 7; $i++) {
            $day = (clone $weekStart)->addDays($i);
            $weekday = (int) $day->dayOfWeekIso - 1; // lunes=0
            
            // Obtener disponibilidades del doctor para este día
            $availabilities = $doctor->availabilities()
                ->where('weekday', $weekday)
                ->orderBy('start_time')
                ->get();
            
            // Obtener citas del día
            $dayAppointments = $appointments->filter(function ($apt) use ($day) {
                return Carbon::parse($apt->start_at)->isSameDay($day);
            })->sortBy('start_at')->values();
            
            $weekData[] = [
                'date' => $day->toDateString(),
                'dayName' => $day->locale('es')->dayName,
                'dayShort' => $day->locale('es')->shortDayName,
                'isToday' => $day->isToday(),
                'isPast' => $day->isPast() && !$day->isToday(),
                'availabilities' => $availabilities,
                'appointments' => $dayAppointments,
                'hasAppointments' => $dayAppointments->count() > 0,
            ];
        }
        
        return $weekData;
    }
    
    /**
     * Completar una cita confirmada
     */
    /**
     * Redirigir al formulario de diagnóstico para completar la cita
     */
    public function complete(Request $request, Appointment $appointment)
    {
        $user = auth()->user();
        $doctor = Doctor::where('user_id', $user->id)->firstOrFail();
        
        // Verificar que la cita pertenece al doctor
        if ($appointment->doctor_id !== $doctor->id) {
            abort(403, 'Esta cita no pertenece a este doctor.');
        }
        
        if ($appointment->status !== 'confirmada') {
            return back()->with('error', 'Solo se pueden completar citas confirmadas.');
        }
        
        // Verificar que la cita sea del día actual o anterior (no futuras)
        $appointmentDate = \Carbon\Carbon::parse($appointment->start_at)->startOfDay();
        $today = \Carbon\Carbon::now()->startOfDay();
        
        if ($appointmentDate->greaterThan($today)) {
            return back()->with('error', 'No se puede completar una cita de días futuros.');
        }
        
        // Verificar que no tenga ya un diagnóstico
        if ($appointment->diagnostic) {
            return back()->with('error', 'Esta cita ya tiene un diagnóstico registrado.');
        }
        
        // Redirigir al formulario de creación de diagnóstico
        return redirect()->route('doctor.diagnostics.create', $appointment->slug);
    }
}
