<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\Appointment;
use App\Models\Diagnostic;
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

        // Crea varios usuarios pacientes de ejemplo
        $patients = [
            ['name' => 'Paciente Ejemplo', 'email' => 'paciente@example.com'],
            ['name' => 'Ana María López', 'email' => 'ana.lopez@example.com'],
            ['name' => 'Pedro González', 'email' => 'pedro.gonzalez@example.com'],
            ['name' => 'Laura Martínez', 'email' => 'laura.martinez@example.com'],
            ['name' => 'Roberto Silva', 'email' => 'roberto.silva@example.com'],
        ];

        foreach ($patients as $patientData) {
            User::factory()->create([
                'name' => $patientData['name'],
                'email' => $patientData['email'],
                'role' => 'patient',
                'password' => bcrypt('password'),
            ]);
        }

        // Crea 3 médicos con sus respectivos usuarios para que puedan iniciar sesión
        $doctorNames = [
            ['name' => 'Dr. Carlos Ramírez', 'specialty' => 'Neumología', 'email' => 'carlos@medicos.com'],
            ['name' => 'Dra. María González', 'specialty' => 'Cardiología', 'email' => 'maria@medicos.com'],
            ['name' => 'Dr. Juan Pérez', 'specialty' => 'Medicina General', 'email' => 'juan@medicos.com'],
        ];

        $doctors = [];
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

            $doctors[] = $doctor;

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

        // Datos de pacientes para las citas
        $appointmentPatients = [
            ['name' => 'Juan Pérez García', 'email' => 'juan.perez@example.com', 'cedula' => '1234567890', 'phone' => '555-0100'],
            ['name' => 'María Rodríguez', 'email' => 'maria.rodriguez@example.com', 'cedula' => '0987654321', 'phone' => '555-0200'],
            ['name' => 'Carlos López', 'email' => 'carlos.lopez@example.com', 'cedula' => '1122334455', 'phone' => '555-0300'],
            ['name' => 'Ana Martínez', 'email' => 'ana.martinez@example.com', 'cedula' => '5544332211', 'phone' => '555-0400'],
            ['name' => 'Pedro Sánchez', 'email' => 'pedro.sanchez@example.com', 'cedula' => '6677889900', 'phone' => '555-0500'],
            ['name' => 'Laura Torres', 'email' => 'laura.torres@example.com', 'cedula' => '9988776655', 'phone' => '555-0600'],
            ['name' => 'Roberto Díaz', 'email' => 'roberto.diaz@example.com', 'cedula' => '3344556677', 'phone' => '555-0700'],
            ['name' => 'Carmen Ruiz', 'email' => 'carmen.ruiz@example.com', 'cedula' => '7766554433', 'phone' => '555-0800'],
        ];

        // Crear citas de prueba variadas
        foreach ($doctors as $index => $doctor) {
            // Citas de ayer (completadas con diagnósticos)
            for ($i = 0; $i < 3; $i++) {
                $patient = $appointmentPatients[$i];
                $appointment = Appointment::create([
                    'doctor_id' => $doctor->id,
                    'slug' => Str::uuid()->toString(),
                    'patient_name' => $patient['name'],
                    'patient_email' => $patient['email'],
                    'cedula_paciente' => $patient['cedula'],
                    'telefono_paciente' => $patient['phone'],
                    'start_at' => Carbon::yesterday()->setTime(8 + $i, 0, 0),
                    'end_at' => Carbon::yesterday()->setTime(8 + $i, 20, 0),
                    'status' => 'completada',
                    'notes' => 'Consulta médica programada',
                ]);

                // Crear diagnóstico para cada cita completada
                Diagnostic::create([
                    'appointment_id' => $appointment->id,
                    'doctor_id' => $doctor->id,
                    'patient_name' => $appointment->patient_name,
                    'patient_email' => $appointment->patient_email,
                    'cedula_paciente' => $appointment->cedula_paciente,
                    'telefono_paciente' => $appointment->telefono_paciente,
                    'fecha_consulta' => $appointment->start_at->toDateString(),
                    'especialidad' => $doctor->specialty,
                    'diagnostico' => $this->getDiagnostico($i),
                    'medicamentos' => $this->getMedicamentos($i),
                    'procedimientos' => $this->getProcedimientos($i),
                    'remisiones' => $i % 2 == 0 ? $this->getRemisiones($i) : null,
                    'observaciones' => 'El paciente presenta mejoría. Se recomienda seguimiento en ' . (7 + $i * 7) . ' días.',
                ]);
            }

            // Citas de hoy (pendientes y confirmadas)
            $patient = $appointmentPatients[3 + $index];
            Appointment::create([
                'doctor_id' => $doctor->id,
                'slug' => Str::uuid()->toString(),
                'patient_name' => $patient['name'],
                'patient_email' => $patient['email'],
                'cedula_paciente' => $patient['cedula'],
                'telefono_paciente' => $patient['phone'],
                'start_at' => Carbon::today()->setTime(10, 0, 0),
                'end_at' => Carbon::today()->setTime(10, 20, 0),
                'status' => $index % 2 == 0 ? 'pendiente' : 'confirmada',
                'notes' => 'Control de rutina',
            ]);

            // Cita de hoy tarde
            $patient = $appointmentPatients[(4 + $index) % count($appointmentPatients)];
            Appointment::create([
                'doctor_id' => $doctor->id,
                'slug' => Str::uuid()->toString(),
                'patient_name' => $patient['name'],
                'patient_email' => $patient['email'],
                'cedula_paciente' => $patient['cedula'],
                'telefono_paciente' => $patient['phone'],
                'start_at' => Carbon::today()->setTime(15, 0, 0),
                'end_at' => Carbon::today()->setTime(15, 20, 0),
                'status' => 'confirmada',
                'notes' => 'Seguimiento de tratamiento',
            ]);

            // Citas de mañana (confirmadas)
            for ($i = 0; $i < 2; $i++) {
                $patient = $appointmentPatients[(5 + $index + $i) % count($appointmentPatients)];
                Appointment::create([
                    'doctor_id' => $doctor->id,
                    'slug' => Str::uuid()->toString(),
                    'patient_name' => $patient['name'],
                    'patient_email' => $patient['email'],
                    'cedula_paciente' => $patient['cedula'],
                    'telefono_paciente' => $patient['phone'],
                    'start_at' => Carbon::tomorrow()->setTime(9 + $i * 2, 0, 0),
                    'end_at' => Carbon::tomorrow()->setTime(9 + $i * 2, 20, 0),
                    'status' => 'confirmada',
                    'notes' => 'Consulta programada',
                ]);
            }

            // Citas de la próxima semana (pendientes y confirmadas)
            for ($day = 2; $day <= 5; $day++) {
                $patient = $appointmentPatients[($day + $index) % count($appointmentPatients)];
                Appointment::create([
                    'doctor_id' => $doctor->id,
                    'slug' => Str::uuid()->toString(),
                    'patient_name' => $patient['name'],
                    'patient_email' => $patient['email'],
                    'cedula_paciente' => $patient['cedula'],
                    'telefono_paciente' => $patient['phone'],
                    'start_at' => Carbon::today()->addDays($day)->setTime(10, 0, 0),
                    'end_at' => Carbon::today()->addDays($day)->setTime(10, 20, 0),
                    'status' => $day % 2 == 0 ? 'pendiente' : 'confirmada',
                    'notes' => 'Primera consulta',
                ]);
            }

            // Algunas citas canceladas
            $patient = $appointmentPatients[(6 + $index) % count($appointmentPatients)];
            Appointment::create([
                'doctor_id' => $doctor->id,
                'slug' => Str::uuid()->toString(),
                'patient_name' => $patient['name'],
                'patient_email' => $patient['email'],
                'cedula_paciente' => $patient['cedula'],
                'telefono_paciente' => $patient['phone'],
                'start_at' => Carbon::today()->addDays(7)->setTime(14, 0, 0),
                'end_at' => Carbon::today()->addDays(7)->setTime(14, 20, 0),
                'status' => 'cancelada',
                'notes' => 'Paciente canceló la cita',
            ]);

            // Cita rechazada
            $patient = $appointmentPatients[(7 + $index) % count($appointmentPatients)];
            Appointment::create([
                'doctor_id' => $doctor->id,
                'slug' => Str::uuid()->toString(),
                'patient_name' => $patient['name'],
                'patient_email' => $patient['email'],
                'cedula_paciente' => $patient['cedula'],
                'telefono_paciente' => $patient['phone'],
                'start_at' => Carbon::today()->addDays(3)->setTime(16, 0, 0),
                'end_at' => Carbon::today()->addDays(3)->setTime(16, 20, 0),
                'status' => 'rechazada',
                'notes' => 'Horario no disponible',
            ]);
        }
    }

    private function getDiagnostico($index): string
    {
        $diagnosticos = [
            'Infección Respiratoria Aguda (IRA) de vías superiores. Se observa congestión nasal, dolor de garganta leve y tos productiva. Sin signos de complicación.',
            'Hipertensión Arterial Esencial (Estadio I). Presión arterial elevada (145/92 mmHg). Paciente asintomático. Se requiere control y modificación de estilo de vida.',
            'Gastritis Aguda. Paciente refiere dolor epigástrico, náuseas ocasionales y sensación de ardor. Asociado a malos hábitos alimenticios.',
            'Cefalea Tensional. Dolor de cabeza bilateral, opresivo, no pulsátil. Relacionado con estrés laboral y malas posturas.',
            'Lumbalgia Mecánica. Dolor en región lumbar baja, de tipo mecánico, sin irradiación. Asociado a esfuerzo físico y sedentarismo.',
        ];
        return $diagnosticos[$index % count($diagnosticos)];
    }

    private function getMedicamentos($index): string
    {
        $medicamentos = [
            "1. Paracetamol 500mg - 1 tableta cada 8 horas por 5 días (para fiebre y dolor)\n2. Loratadina 10mg - 1 tableta al día por 7 días (antihistamínico)\n3. Amoxicilina 500mg - 1 cápsula cada 8 horas por 7 días (antibiótico)",
            "1. Losartán 50mg - 1 tableta al día en la mañana\n2. Amlodipino 5mg - 1 tableta al día\n3. Ácido Acetilsalicílico 100mg - 1 tableta al día (antiagregante)",
            "1. Omeprazol 20mg - 1 cápsula 30 minutos antes del desayuno por 14 días\n2. Sucralfato suspensión - 1 cucharada 4 veces al día antes de comidas\n3. Dimenhidrinato 50mg - 1 tableta cada 8 horas si hay náuseas",
            "1. Ibuprofeno 400mg - 1 tableta cada 8 horas por 5 días con alimentos\n2. Complejo B - 1 tableta al día por 30 días\n3. Relajante muscular (Ciclobenzaprina 10mg) - 1 tableta antes de dormir por 7 días",
            "1. Diclofenaco 50mg - 1 tableta cada 12 horas por 7 días con alimentos\n2. Vitamina B12 1000mcg - 1 tableta al día por 30 días\n3. Gel antiinflamatorio tópico - aplicar 3 veces al día en zona lumbar",
        ];
        return $medicamentos[$index % count($medicamentos)];
    }

    private function getProcedimientos($index): string
    {
        $procedimientos = [
            "1. Reposo relativo por 3-5 días\n2. Hidratación abundante (2-3 litros de agua al día)\n3. Inhalaciones con vapor 2-3 veces al día\n4. Evitar cambios bruscos de temperatura",
            "1. Control de presión arterial diario (mañana y noche)\n2. Dieta hiposódica (baja en sal)\n3. Ejercicio moderado 30 minutos diarios (caminata)\n4. Control de peso semanal\n5. Laboratorios de control en 1 mes (perfil lipídico, glucosa)",
            "1. Dieta blanda por 5-7 días\n2. Evitar irritantes: café, alcohol, picantes, grasas\n3. Comidas pequeñas y frecuentes (5-6 al día)\n4. No acostarse inmediatamente después de comer\n5. Evitar AINEs (antiinflamatorios)",
            "1. Aplicar compresas calientes en cuello y hombros 15-20 minutos\n2. Ejercicios de estiramiento cervical suaves\n3. Mejorar ergonomía del puesto de trabajo\n4. Técnicas de relajación y manejo de estrés\n5. Evitar pantallas por períodos prolongados",
            "1. Reposo relativo por 3-5 días (evitar carga pesada)\n2. Aplicar compresas frías primeras 48h, luego calientes\n3. Ejercicios de estiramiento lumbar suaves\n4. Corregir postura al sentarse y al dormir\n5. Fisioterapia (10 sesiones) si no mejora en 1 semana",
        ];
        return $procedimientos[$index % count($procedimientos)];
    }

    private function getRemisiones($index): ?string
    {
        $remisiones = [
            "Remisión a Otorrinolaringología para valoración de posible sinusitis crónica si los síntomas persisten después de 14 días de tratamiento.",
            "Remisión a Cardiología para evaluación integral y ajuste de tratamiento antihipertensivo. Solicitar EKG y Ecocardiograma.",
            null, // Sin remisión
            "Remisión a Neurología si las cefaleas persisten o aumentan en frecuencia/intensidad. Considerar estudios de imagen (TAC o RMN cerebral).",
            "Remisión a Medicina Física y Rehabilitación para programa de rehabilitación lumbar. Solicitar Radiografía de columna lumbosacra AP y lateral.",
        ];
        return $remisiones[$index % count($remisiones)];
    }
}