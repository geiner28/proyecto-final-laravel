<?php

namespace App\Http\Controllers;

use App\Models\Diagnostic;
use App\Models\Appointment;
use App\Mail\DiagnosticCreatedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class DiagnosticController extends Controller
{
    /**
     * Lista de diagnósticos según el rol del usuario
     * Admin: ve todos
     * Doctor: ve todos los diagnósticos de sus pacientes (histórico completo)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Diagnostic::with(['doctor', 'appointment'])
            ->orderBy('created_at', 'desc');
        
        // Si es doctor, puede ver todos los diagnósticos de pacientes que ha atendido
        // (historia clínica completa, no importa el médico que lo atendió antes)
        if ($user->role === 'doctor') {
            // Obtener todos los pacientes que este doctor ha atendido
            $pacientesCedulas = Appointment::where('doctor_id', $user->doctor->id)
                ->distinct()
                ->pluck('cedula_paciente');
            
            $query->whereIn('cedula_paciente', $pacientesCedulas);
        }
        
        // Filtros de búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('patient_name', 'like', "%{$search}%")
                  ->orWhere('cedula_paciente', 'like', "%{$search}%")
                  ->orWhere('patient_email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('cedula')) {
            $query->where('cedula_paciente', $request->cedula);
        }
        
        $diagnostics = $query->paginate(20);
        
        $viewName = $user->role === 'admin' ? 'Admin/Diagnostics/Index' : 'Doctor/Diagnostics/Index';
        
        return Inertia::render($viewName, [
            'diagnostics' => $diagnostics,
            'filters' => $request->only(['search', 'cedula']),
        ]);
    }
    
    /**
     * Formulario para crear diagnóstico al completar una cita
     */
    public function create(Appointment $appointment)
    {
        // Verificar que la cita pertenece al doctor autenticado
        $user = Auth::user();
        if ($user->role === 'doctor' && $appointment->doctor_id !== $user->doctor->id) {
            abort(403, 'No tienes permiso para crear un diagnóstico para esta cita');
        }
        
        // Verificar que la cita no tiene ya un diagnóstico
        if ($appointment->diagnostic) {
            return redirect()->route('doctor.appointments.index')
                ->with('error', 'Esta cita ya tiene un diagnóstico registrado');
        }
        
        return Inertia::render('Doctor/Diagnostics/Create', [
            'appointment' => $appointment->load('doctor'),
        ]);
    }
    
    /**
     * Guardar el diagnóstico y completar la cita
     */
    public function store(Request $request, Appointment $appointment)
    {
        $user = Auth::user();
        
        // Verificar permisos
        if ($user->role === 'doctor' && $appointment->doctor_id !== $user->doctor->id) {
            abort(403, 'No tienes permiso para crear un diagnóstico para esta cita');
        }
        
        $validated = $request->validate([
            'diagnostico' => 'required|string|min:10',
            'medicamentos' => 'nullable|string',
            'procedimientos' => 'nullable|string',
            'remisiones' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);
        
        // Crear el diagnóstico
        $diagnostic = Diagnostic::create([
            'appointment_id' => $appointment->id,
            'doctor_id' => $appointment->doctor->user_id, // Corregido: usar user_id del doctor, no el doctor_id
            'patient_name' => $appointment->patient_name,
            'patient_email' => $appointment->patient_email,
            'cedula_paciente' => $appointment->cedula_paciente,
            'telefono_paciente' => $appointment->telefono_paciente,
            'diagnostico' => $validated['diagnostico'],
            'medicamentos' => $validated['medicamentos'],
            'procedimientos' => $validated['procedimientos'],
            'remisiones' => $validated['remisiones'],
            'observaciones' => $validated['observaciones'],
            'fecha_consulta' => $appointment->start_at->toDateString(),
            'especialidad' => $appointment->doctor->specialty ?? null,
        ]);
        
        // Marcar la cita como completada
        $appointment->update(['status' => 'completada']);
        
        // Enviar email al paciente con el diagnóstico
        try {
            Mail::to($appointment->patient_email)->send(new DiagnosticCreatedMail($diagnostic));
        } catch (\Exception $e) {
            // Log error pero no fallar
            \Log::error('Error enviando email de diagnóstico: ' . $e->getMessage());
        }
        
        return redirect()->route('doctor.appointments.index')
            ->with('success', 'Diagnóstico creado exitosamente. Se ha enviado una copia al paciente por correo.');
    }
    
    /**
     * Ver detalles de un diagnóstico
     */
    public function show(Diagnostic $diagnostic)
    {
        $user = Auth::user();
        
        // Verificar permisos
        if ($user->role === 'doctor') {
            // Doctor puede ver si es de uno de sus pacientes
            $pacientesCedulas = Appointment::where('doctor_id', $user->doctor->id)
                ->distinct()
                ->pluck('cedula_paciente');
            
            if (!$pacientesCedulas->contains($diagnostic->cedula_paciente)) {
                abort(403, 'No tienes permiso para ver este diagnóstico');
            }
        }
        
        $diagnostic->load(['doctor', 'appointment']);
        
        return Inertia::render('Diagnostics/Show', [
            'diagnostic' => $diagnostic,
        ]);
    }
    
    /**
     * Descargar diagnóstico como PDF
     */
    public function downloadPDF(Diagnostic $diagnostic)
    {
        $user = Auth::user();
        
        // Verificar permisos si es doctor
        if ($user && $user->role === 'doctor') {
            $pacientesCedulas = Appointment::where('doctor_id', $user->doctor->id)
                ->distinct()
                ->pluck('cedula_paciente');
            
            if (!$pacientesCedulas->contains($diagnostic->cedula_paciente)) {
                abort(403, 'No tienes permiso para descargar este diagnóstico');
            }
        }
        
        $diagnostic->load(['doctor', 'appointment']);
        
        $pdf = Pdf::loadView('pdf.diagnostic', compact('diagnostic'));
        
        $filename = 'diagnostico_' . $diagnostic->cedula_paciente . '_' . $diagnostic->id . '.pdf';
        
        return $pdf->download($filename);
    }
    
    /**
     * Consulta pública de diagnósticos por cédula (sin autenticación)
     */
    public function publicSearch(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'cedula' => 'required|string',
            ]);
            
            $diagnostics = Diagnostic::where('cedula_paciente', $validated['cedula'])
                ->with(['doctor', 'appointment'])
                ->orderBy('fecha_consulta', 'desc')
                ->get();
            
            return Inertia::render('Public/ConsultarDiagnostico', [
                'diagnostics' => $diagnostics,
                'cedula' => $validated['cedula'],
            ]);
        }
        
        return Inertia::render('Public/ConsultarDiagnostico', [
            'diagnostics' => [],
            'cedula' => null,
        ]);
    }
    
    /**
     * Historia clínica completa de un paciente por cédula (solo para admin)
     */
    public function patientHistory(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403, 'No tienes permiso para acceder a esta funcionalidad');
        }
        
        $cedula = $request->input('cedula');
        
        if (!$cedula) {
            return Inertia::render('Admin/Diagnostics/PatientHistory', [
                'diagnostics' => [],
                'appointments' => [],
                'patient' => null,
                'cedula' => null,
            ]);
        }
        
        // Buscar todos los diagnósticos del paciente
        $diagnostics = Diagnostic::where('cedula_paciente', $cedula)
            ->with(['doctor', 'appointment'])
            ->orderBy('fecha_consulta', 'desc')
            ->get();
        
        // Buscar todas las citas del paciente
        $appointments = Appointment::where('cedula_paciente', $cedula)
            ->with(['doctor'])
            ->orderBy('start_at', 'desc')
            ->get();
        
        $patient = null;
        if ($diagnostics->count() > 0) {
            $patient = [
                'name' => $diagnostics->first()->patient_name,
                'email' => $diagnostics->first()->patient_email,
                'cedula' => $diagnostics->first()->cedula_paciente,
                'telefono' => $diagnostics->first()->telefono_paciente,
            ];
        } elseif ($appointments->count() > 0) {
            $patient = [
                'name' => $appointments->first()->patient_name,
                'email' => $appointments->first()->patient_email,
                'cedula' => $appointments->first()->cedula_paciente,
                'telefono' => $appointments->first()->telefono_paciente,
            ];
        }
        
        return Inertia::render('Admin/Diagnostics/PatientHistory', [
            'diagnostics' => $diagnostics,
            'appointments' => $appointments,
            'patient' => $patient,
            'cedula' => $cedula,
        ]);
    }
}
