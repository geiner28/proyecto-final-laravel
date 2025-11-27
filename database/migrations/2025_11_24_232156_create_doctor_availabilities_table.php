<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones: crea la tabla de disponibilidad semanal por médico.
     */
    public function up(): void
    {
        Schema::create('doctor_availabilities', function (Blueprint $table) {
            // Llave primaria
            $table->id();
            // Relación al médico; al borrar el médico se borran sus disponibilidades
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            // Día de la semana (0=lunes ... 6=domingo)
            $table->unsignedTinyInteger('weekday');
            // Franja horaria de atención
            $table->time('start_time');
            $table->time('end_time');
            // Índice único para evitar duplicados exactos por día y franja
            $table->unique(['doctor_id', 'weekday', 'start_time', 'end_time']);
            $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_availabilities');
    }
};
