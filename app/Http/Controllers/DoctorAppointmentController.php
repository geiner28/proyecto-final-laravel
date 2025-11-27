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

class DoctorAppointmentController extends Controller
{
    /**
     * Mostrar las citas del doctor autenticado
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Obtener el doctor asociado al usuario
        $doctor = Doctor::where('user_id', $user->id)->firstOrFail();
        
        // Query base: citas del doctor
        $query = Appointment::where('doctor_id', $doctor->id)
            ->with(['doctor'])
            ->orderBy('start_at', 'desc');
        
        // Filtro por búsqueda (nombre o cédula del paciente)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('patient_name', 'ILIKE', "%{$search}%")
                  ->orWhere('cedula_paciente', 'ILIKE', "%{$search}%");
            });
        }
        
        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $appointments = $query->paginate(10)->withQueryString();
        
        return Inertia::render('Doctor/Appointments', [
            'appointments' => $appointments,
            'doctor' => $doctor,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
            ],
        ]);
    }
    
    /**
     * Completar una cita confirmada
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
        
        // Actualizar estado y notas
        $appointment->update([
            'status' => 'completada',
            'notes' => $request->input('notes', $appointment->notes)
        ]);
        
        return back()->with('success', 'Cita marcada como completada.');
    }
}
