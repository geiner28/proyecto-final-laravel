<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    /** @use HasFactory<\Database\Factories\AppointmentFactory> */
    use HasFactory;

    // Campos asignables masivamente
    protected $fillable = [
        'doctor_id',
        'slug',
        'patient_name',
        'patient_email',
        'cedula_paciente',
        'telefono_paciente',
        'start_at',
        'end_at',
        'status',
        'notes',
    ];

    /**
     * Casts para conversión automática de tipos
     */
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    /**
     * Model binding por 'slug' para las citas.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Relación: una cita pertenece a un médico.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Relación: una cita puede tener un diagnóstico.
     */
    public function diagnostic()
    {
        return $this->hasOne(Diagnostic::class);
    }
}
