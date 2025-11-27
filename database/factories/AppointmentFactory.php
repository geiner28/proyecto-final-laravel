<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Genera una cita ficticia en estado pendiente para pruebas
        $start = Carbon::now()->addDays(1)->setTime(10, 0);
        $duration = (int) (env('APPOINTMENT_DURATION_MINUTES', 20));
        return [
            'doctor_id' => null, // se asigna al crear
            'slug' => Str::uuid()->toString(),
            'patient_name' => $this->faker->name(),
            'patient_email' => $this->faker->safeEmail(),
            'start_at' => $start,
            'end_at' => (clone $start)->addMinutes($duration),
            'status' => 'pendiente',
            'notes' => $this->faker->sentence(),
        ];
    }
}
