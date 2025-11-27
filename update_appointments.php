<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔄 Actualizando TODAS las citas confirmadas a HOY (26 nov)...\n\n";

// Actualizar TODAS las citas confirmadas a HOY
$updated = DB::table('appointments')
    ->where('status', 'confirmada')
    ->update([
        'start_at' => '2025-11-26 10:00:00',
        'end_at' => '2025-11-26 10:20:00',
        'updated_at' => now()
    ]);

echo "✅ Actualizadas {$updated} citas confirmadas a HOY (26 nov)\n\n";

// Verificar por doctor
$doctors = DB::table('doctors')->get(['id', 'name']);

foreach ($doctors as $doc) {
    $citas = DB::table('appointments')
        ->where('doctor_id', $doc->id)
        ->where('status', 'confirmada')
        ->orderBy('start_at')
        ->get(['patient_name', 'status', 'start_at']);
    
    if ($citas->count() > 0) {
        echo "📋 {$doc->name}: {$citas->count()} cita(s) de HOY\n";
        foreach ($citas as $c) {
            echo "  • {$c->patient_name}\n";
        }
        echo "\n";
    }
}
