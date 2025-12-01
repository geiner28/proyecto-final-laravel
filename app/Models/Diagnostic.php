<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diagnostic extends Model
{
    protected $fillable = [
        'appointment_id',
        'doctor_id',
        'patient_name',
        'patient_email',
        'cedula_paciente',
        'telefono_paciente',
        'diagnostico',
        'medicamentos',
        'procedimientos',
        'remisiones',
        'observaciones',
        'fecha_consulta',
        'especialidad',
    ];

    protected $casts = [
        'fecha_consulta' => 'date',
    ];

    /**
     * Relación con la cita
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Relación con el médico que realizó el diagnóstico
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
    
    /**
     * Relación con el perfil del médico (tabla doctors)
     */
    public function doctorProfile(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'user_id');
    }
}
