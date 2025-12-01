<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('diagnostics', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            
            // Datos del paciente (desnormalizados para histórico)
            $table->string('patient_name');
            $table->string('patient_email');
            $table->string('cedula_paciente')->index();
            $table->string('telefono_paciente')->nullable();
            
            // Diagnóstico médico
            $table->text('diagnostico'); // Diagnóstico principal
            $table->text('medicamentos')->nullable(); // Medicamentos recetados
            $table->text('procedimientos')->nullable(); // Procedimientos realizados
            $table->text('remisiones')->nullable(); // Remisiones a especialistas
            $table->text('observaciones')->nullable(); // Observaciones adicionales
            
            // Metadata
            $table->date('fecha_consulta');
            $table->string('especialidad')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnostics');
    }
};
