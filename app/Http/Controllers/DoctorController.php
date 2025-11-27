<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lista de médicos con filtros simples
        $doctors = Doctor::query()->orderBy('name')->get();
        return Inertia::render('Doctors/Index', [
            'doctors' => $doctors,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Formulario de creación
        return Inertia::render('Doctors/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Valida y crea un médico nuevo con sus credenciales de acceso
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','unique:users,email'],
            'specialty' => ['required','string','max:255'],
            'password' => ['required','string','min:8','confirmed'],
            'availability' => ['nullable','array'],
        ]);

        // Crear el usuario asociado con rol 'doctor'
        $user = \App\Models\User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => \Hash::make($data['password']),
            'role' => 'doctor',
        ]);

        // Crear el registro del médico vinculado al usuario
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'specialty' => $data['specialty'],
            'slug' => Str::slug($data['name'].'-'.Str::random(6)),
        ]);

        // Guardar la disponibilidad horaria
        if (isset($data['availability'])) {
            foreach ($data['availability'] as $weekday => $schedule) {
                if (isset($schedule['enabled']) && $schedule['enabled']) {
                    \App\Models\DoctorAvailability::create([
                        'doctor_id' => $doctor->id,
                        'weekday' => (int)$weekday,
                        'start_time' => $schedule['start'],
                        'end_time' => $schedule['end'],
                    ]);
                }
            }
        }

        return redirect()->route('panel.doctors.index')->with('success', 'Médico creado exitosamente con su horario de disponibilidad.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor)
    {
        // Muestra detalle básico del médico
        return Inertia::render('Doctors/Show', [
            'doctor' => $doctor,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Doctor $doctor)
    {
        // Cargar disponibilidad actual del médico
        $doctor->load('availabilities');
        
        // Formulario de edición
        return Inertia::render('Doctors/Edit', [
            'doctor' => $doctor,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Doctor $doctor)
    {
        // Valida y actualiza datos del médico
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','unique:users,email,' . ($doctor->user_id ?? 'NULL')],
            'specialty' => ['required','string','max:255'],
            'password' => ['nullable','string','min:8','confirmed'],
            'availability' => ['nullable','array'],
        ]);

        // Actualizar datos del médico
        $doctor->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'specialty' => $data['specialty'],
        ]);

        // Si el médico tiene un usuario vinculado, actualizar sus credenciales
        if ($doctor->user_id) {
            $user = \App\Models\User::find($doctor->user_id);
            if ($user) {
                $user->update([
                    'name' => $data['name'],
                    'email' => $data['email'],
                ]);

                // Si se proporcionó una nueva contraseña, actualizarla
                if (!empty($data['password'])) {
                    $user->update([
                        'password' => \Hash::make($data['password']),
                    ]);
                }
            }
        }

        // Sincronizar disponibilidad horaria
        if (isset($data['availability'])) {
            // Eliminar disponibilidad existente
            \App\Models\DoctorAvailability::where('doctor_id', $doctor->id)->delete();
            
            // Crear nueva disponibilidad
            foreach ($data['availability'] as $weekday => $schedule) {
                if (isset($schedule['enabled']) && $schedule['enabled']) {
                    \App\Models\DoctorAvailability::create([
                        'doctor_id' => $doctor->id,
                        'weekday' => (int)$weekday,
                        'start_time' => $schedule['start'],
                        'end_time' => $schedule['end'],
                    ]);
                }
            }
        }

        return redirect()->route('panel.doctors.index')->with('success', 'Médico actualizado exitosamente con su horario de disponibilidad');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor)
    {
        // Elimina el médico
        $doctor->delete();
        return redirect()->route('panel.doctors.index')->with('success', 'Médico eliminado');
    }
}
