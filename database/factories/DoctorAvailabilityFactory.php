<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DoctorAvailability>
 */
class DoctorAvailabilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Genera una franja de disponibilidad en horario laboral
        $startHour = $this->faker->numberBetween(8, 12);
        $endHour = $startHour + $this->faker->numberBetween(4, 8);
        return [
            'doctor_id' => null, // se asigna al crear
            'weekday' => $this->faker->numberBetween(0, 6),
            'start_time' => sprintf('%02d:00:00', $startHour),
            'end_time' => sprintf('%02d:00:00', $endHour),
        ];
    }
}
