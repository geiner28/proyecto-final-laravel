<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\Appointment;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crea un usuario administrador
        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@hospital.com',
            'role' => 'admin',
            'password' => bcrypt('admin123'),
        ]);

        // Crea un usuario paciente de ejemplo
        User::factory()->create([
            'name' => 'Paciente Ejemplo',
            'email' => 'paciente@example.com',
            'role' => 'patient',
            'password' => bcrypt('password'),
        ]);

        // Crea 3 médicos con sus respectivos usuarios para que puedan iniciar sesión
        $doctorNames = [
            ['name' => 'Dr. Carlos Ramírez', 'specialty' => 'Neumología', 'email' => 'carlos@medicos.com'],
            ['name' => 'Dra. María González', 'specialty' => 'Neumología', 'email' => 'maria@medicos.com'],
            ['name' => 'Dr. Juan Pérez', 'specialty' => 'Neumología', 'email' => 'juan@medicos.com'],
        ];

        foreach ($doctorNames as $doctorData) {
            // Crea el usuario médico con contraseña "password"
            $user = User::factory()->create([
                'name' => $doctorData['name'],
                'email' => $doctorData['email'],
                'role' => 'doctor',
                'password' => bcrypt('password'),
            ]);

            // Crea el perfil de médico asociado al usuario
            $doctor = Doctor::create([
                'user_id' => $user->id,
                'name' => $doctorData['name'],
                'slug' => Str::slug($doctorData['name']) . '-' . Str::random(6),
                'specialty' => $doctorData['specialty'],
                'email' => $doctorData['email'],
            ]);

            // Crea disponibilidad estándar lunes-viernes
            // Turno mañana: 8:00 AM - 12:00 PM
            // Turno tarde: 2:00 PM - 6:00 PM
            foreach ([0,1,2,3,4] as $weekday) { // lunes a viernes (0=lunes, 4=viernes)
                // Turno de mañana
                DoctorAvailability::create([
                    'doctor_id' => $doctor->id,
                    'weekday' => $weekday,
                    'start_time' => '08:00:00',
                    'end_time' => '12:00:00',
                ]);
                // Turno de tarde
                DoctorAvailability::create([
                    'doctor_id' => $doctor->id,
                    'weekday' => $weekday,
                    'start_time' => '14:00:00',
                    'end_time' => '18:00:00',
                ]);
            }
        }

        // Crear citas de prueba para poder probar cancelar/reagendar
        $doctors = Doctor::all();
        if ($doctors->count() > 0) {
            $firstDoctor = $doctors->first();
            
            // Cita pendiente para hoy más tarde (se puede cancelar/reagendar)
            Appointment::create([
                'doctor_id' => $firstDoctor->id,
                'slug' => Str::uuid()->toString(),
                'patient_name' => 'Juan Pérez García',
                'patient_email' => 'juan.perez@example.com',
                'cedula_paciente' => '1234567890',
                'telefono_paciente' => '555-0100',
                'start_at' => Carbon::today()->setTime(15, 0, 0), // Hoy 3:00 PM
                'end_at' => Carbon::today()->setTime(15, 20, 0),
                'status' => 'pendiente',
                'notes' => 'Control de rutina',
            ]);

            // Cita confirmada para mañana (se puede cancelar/reagendar)
            Appointment::create([
                'doctor_id' => $firstDoctor->id,
                'slug' => Str::uuid()->toString(),
                'patient_name' => 'Juan Pérez García',
                'patient_email' => 'juan.perez@example.com',
                'cedula_paciente' => '1234567890',
                'telefono_paciente' => '555-0100',
                'start_at' => Carbon::tomorrow()->setTime(10, 0, 0), // Mañana 10:00 AM
                'end_at' => Carbon::tomorrow()->setTime(10, 20, 0),
                'status' => 'confirmada',
                'notes' => 'Consulta de seguimiento',
            ]);

            // Cita pasada completada (no se puede modificar)
            Appointment::create([
                'doctor_id' => $firstDoctor->id,
                'slug' => Str::uuid()->toString(),
                'patient_name' => 'Juan Pérez García',
                'patient_email' => 'juan.perez@example.com',
                'cedula_paciente' => '1234567890',
                'telefono_paciente' => '555-0100',
                'start_at' => Carbon::yesterday()->setTime(9, 0, 0), // Ayer 9:00 AM
                'end_at' => Carbon::yesterday()->setTime(9, 20, 0),
                'status' => 'completada',
                'notes' => 'Consulta general',
            ]);

            // Cita cancelada (no se puede modificar)
            Appointment::create([
                'doctor_id' => $firstDoctor->id,
                'slug' => Str::uuid()->toString(),
                'patient_name' => 'Juan Pérez García',
                'patient_email' => 'juan.perez@example.com',
                'cedula_paciente' => '1234567890',
                'telefono_paciente' => '555-0100',
                'start_at' => Carbon::today()->addDays(2)->setTime(14, 0, 0),
                'end_at' => Carbon::today()->addDays(2)->setTime(14, 20, 0),
                'status' => 'cancelada',
                'notes' => 'Cita previamente cancelada',
            ]);
        }
    }
}