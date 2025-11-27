<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorAvailability extends Model
{
    /** @use HasFactory<\Database\Factories\DoctorAvailabilityFactory> */
    use HasFactory;

    // Campos asignables masivamente
    protected $fillable = [
        'doctor_id',
        'weekday',
        'start_time',
        'end_time',
    ];

    /**
     * Relación: esta disponibilidad pertenece a un médico.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
