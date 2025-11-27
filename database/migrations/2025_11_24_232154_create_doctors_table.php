<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones: crea la tabla de médicos.
     */
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            // Llave primaria
            $table->id();
            // Relación opcional al usuario (Jetstream), para que el médico pueda iniciar sesión
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            // Nombre del médico
            $table->string('name');
            // Slug único para enlazar por URL (model binding por slug)
            $table->string('slug')->unique();
            // Especialidad médica (neumología por defecto)
            $table->string('specialty')->default('Neumología');
            // Correo opcional del médico
            $table->string('email')->nullable();
            // Timestamps de Laravel
            $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
