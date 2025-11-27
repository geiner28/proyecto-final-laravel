<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Genera datos de prueba para un médico de neumología
        $name = $this->faker->name();
        return [
            'user_id' => null, // por defecto sin usuario asignado
            'name' => $name,
            'slug' => Str::slug($name . '-' . Str::random(6)),
            'specialty' => 'Neumología',
            'email' => $this->faker->safeEmail(),
        ];
    }
}
