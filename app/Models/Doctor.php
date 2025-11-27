<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    /** @use HasFactory<\Database\Factories\DoctorFactory> */
    use HasFactory;

    // Campos asignables masivamente
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'specialty',
        'email',
    ];

    /**
     * Model binding por 'slug' en las rutas.
     * Esto permite que Laravel busque el médico por el campo 'slug'.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Relación: un médico tiene muchas disponibilidades semanales.
     */
    public function availabilities()
    {
        return $this->hasMany(DoctorAvailability::class);
    }

    /**
     * Relación: un médico tiene muchas citas.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Relación: un médico pertenece a un usuario (opcional).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
