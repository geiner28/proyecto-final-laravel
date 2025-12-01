<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Doctor;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\PublicAppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorAppointmentController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DiagnosticController;

// Landing inicial: página pública sin autenticación
Route::get('/', function () {
    // Proporcionamos el slug del primer médico para permitir acceso rápido al calendario
    $firstDoctorSlug = Doctor::orderBy('name')->value('slug');
    return Inertia::render('Landing', [
        'firstDoctorSlug' => $firstDoctorSlug,
    ]);
})->name('landing');

// Rutas públicas (explorar disponibilidad y reservar)
// GET "/explorar": Selector de médico y calendario semanal de disponibilidad
Route::get('/explorar', [PublicController::class, 'index'])->name('public.index');
// GET "/doctors/{slug}": Perfil básico y próximos espacios disponibles
Route::get('/doctors/{doctor}', [PublicController::class, 'doctor'])->name('public.doctor');
// GET "/appointments/new": Formulario para confirmar datos y reservar
Route::get('/appointments/new', [PublicController::class, 'appointmentNew'])->name('public.appointments.new');
// POST "/appointments": Crea cita en pendiente; valida colisiones y disponibilidad y envía email
Route::post('/appointments', [PublicAppointmentController::class, 'store'])->name('public.appointments.store');

// Consulta pública de citas por cédula
Route::get('/consultar-cita', [PublicAppointmentController::class, 'consultaForm'])->name('public.consultar.form');
Route::post('/consultar-cita', [PublicAppointmentController::class, 'consultar'])->name('public.consultar');

// Consulta pública de diagnósticos por cédula (sin autenticación)
Route::get('/consultar-diagnostico', [DiagnosticController::class, 'publicSearch'])->name('public.diagnostics.search');
Route::post('/consultar-diagnostico', [DiagnosticController::class, 'publicSearch'])->name('public.diagnostics.search.post');
// Descargar PDF de diagnóstico (público)
Route::get('/diagnostics/{diagnostic}/pdf', [DiagnosticController::class, 'downloadPDF'])->name('public.diagnostics.pdf');

// Cancelar y reagendar citas (paciente)
Route::post('/appointments/{appointment:slug}/cancel', [PublicAppointmentController::class, 'cancel'])->name('public.appointments.cancel');
Route::get('/appointments/{appointment:slug}/reschedule', [PublicAppointmentController::class, 'rescheduleForm'])->name('public.appointments.reschedule-form');
Route::post('/appointments/{appointment:slug}/reschedule', [PublicAppointmentController::class, 'reschedule'])->name('public.appointments.reschedule');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Panel protegido (Jetstream)
    // GET "/home": Resumen de pendientes y próximas confirmadas con filtro por médico
    Route::get('/home', [AppointmentController::class, 'home'])->name('panel.home');

    // GET "/calendar": Calendario semanal con citas y huecos del médico (panel)
    Route::get('/calendar', [AppointmentController::class, 'calendar'])->name('panel.calendar');

    // Prefijo para evitar colisiones con rutas públicas
    Route::prefix('admin')->middleware(['admin'])->group(function () {
        // Resource "/admin/doctors": CRUD con slug
        Route::resource('/doctors', DoctorController::class)->names('panel.doctors');

        // Resource "/admin/appointments": Listado con filtros; acciones aceptar/rechazar/cancelar
        Route::resource('/appointments', AppointmentController::class)->names('panel.appointments');
        // Aceptar y rechazar citas pendientes
        Route::post('/appointments/{appointment:slug}/accept', [AppointmentController::class, 'accept'])->name('panel.appointments.accept');
        Route::post('/appointments/{appointment:slug}/reject', [AppointmentController::class, 'reject'])->name('panel.appointments.reject');
        // Cancelar y reagendar citas (admin)
        Route::post('/appointments/{appointment:slug}/cancel', [AppointmentController::class, 'cancel'])->name('panel.appointments.cancel');
        Route::get('/appointments/{appointment:slug}/reschedule', [AppointmentController::class, 'rescheduleForm'])->name('panel.appointments.reschedule-form');
        Route::post('/appointments/{appointment:slug}/reschedule', [AppointmentController::class, 'reschedule'])->name('panel.appointments.reschedule');
        
        // Diagnósticos (admin)
        Route::get('/diagnostics', [DiagnosticController::class, 'index'])->name('admin.diagnostics.index');
        Route::get('/diagnostics/{diagnostic}', [DiagnosticController::class, 'show'])->name('admin.diagnostics.show');
        Route::get('/diagnostics/{diagnostic}/pdf', [DiagnosticController::class, 'downloadPDF'])->name('admin.diagnostics.pdf');
        Route::get('/patient-history', [DiagnosticController::class, 'patientHistory'])->name('admin.diagnostics.patient-history');
    });
    
    // Rutas para médicos
    Route::prefix('doctor')->middleware(['doctor'])->group(function () {
        Route::get('/appointments', [DoctorAppointmentController::class, 'index'])->name('doctor.appointments.index');
        Route::post('/appointments/{appointment:slug}/complete', [DoctorAppointmentController::class, 'complete'])->name('doctor.appointments.complete');
        
        // Diagnósticos (doctor)
        Route::get('/diagnostics', [DiagnosticController::class, 'index'])->name('doctor.diagnostics.index');
        Route::get('/diagnostics/create/{appointment:slug}', [DiagnosticController::class, 'create'])->name('doctor.diagnostics.create');
        Route::post('/diagnostics/store/{appointment:slug}', [DiagnosticController::class, 'store'])->name('doctor.diagnostics.store');
        Route::get('/diagnostics/{diagnostic}', [DiagnosticController::class, 'show'])->name('doctor.diagnostics.show');
        Route::get('/diagnostics/{diagnostic}/pdf', [DiagnosticController::class, 'downloadPDF'])->name('doctor.diagnostics.pdf');
    });
});
