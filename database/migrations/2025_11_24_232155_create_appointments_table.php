<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones: crea la tabla de citas.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            // Llave primaria
            $table->id();
            // Relación a médico; si se elimina el médico, se eliminan sus citas
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            // Slug único para enlazar por URL (model binding por slug)
            $table->string('slug')->unique();
            // Datos del paciente
            $table->string('patient_name');
            $table->string('patient_email');
            // Inicio y fin de la cita
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            // Estado de la cita: pendiente, confirmada, completada, rechazada
            $table->string('status')->index();
            // Notas opcionales
            $table->text('notes')->nullable();
            // Índices útiles para consultas de disponibilidad
            $table->index(['doctor_id', 'start_at']);
            $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
