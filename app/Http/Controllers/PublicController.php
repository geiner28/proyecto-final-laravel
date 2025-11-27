<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Doctor;
use App\Models\Appointment;
use Carbon\Carbon;

class PublicController extends Controller
{
    /**
     * Muestra la página principal pública con lista de médicos disponibles.
     */
    public function index(Request $request)
    {
        // Obtiene todos los médicos activos ordenados por nombre
        $doctors = Doctor::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('Public/Index', [
            'doctors' => $doctors,
        ]);
    }

    /**
     * Muestra el perfil del médico y próximos espacios disponibles.
     */
    public function doctor(Doctor $doctor, Request $request)
    {
        // Mostrar disponibilidad de los próximos 2 meses
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = (clone $startOfMonth)->addMonths(2)->endOfMonth();
        $duration = (int) env('APPOINTMENT_DURATION_MINUTES', 20);
        $availability = $this->calcularDisponibilidadRango($doctor, $startOfMonth, $endOfMonth, $duration);

        return Inertia::render('Public/Doctor', [
            'doctor' => $doctor,
            'weekStart' => Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString(),
            'duration' => $duration,
            'availability' => $availability,
        ]);
    }

    /**
     * Muestra el formulario para confirmar datos y reservar la cita.
     */
    public function appointmentNew(Request $request)
    {
        $doctor = Doctor::where('slug', $request->get('doctor'))->firstOrFail();
        $start = Carbon::parse($request->get('start'));
        $duration = (int) env('APPOINTMENT_DURATION_MINUTES', 20);

        // Calcular disponibilidad solo del mes de la fecha seleccionada
        // Limitar a 2 meses hacia adelante para no sobrecargar
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = (clone $startOfMonth)->addMonths(2)->endOfMonth();
        
        $availability = $this->calcularDisponibilidadRango($doctor, $startOfMonth, $endOfMonth, $duration);

        return Inertia::render('Public/AppointmentNew', [
            'doctor' => $doctor,
            'start' => $start->toIso8601String(),
            'duration' => $duration,
            'availability' => $availability,
        ]);
    }

    /**
     * Calcula la disponibilidad semanal del médico.
     * Solo retorna espacios no ocupados por citas pendientes o confirmadas.
     */
    protected function calcularDisponibilidad(Doctor $doctor, Carbon $startOfWeek, int $duration): array
    {
        $slots = [];
        // Construye un mapa de citas existentes para la semana (pendientes/confirmadas)
        $appointments = $doctor->appointments()
            ->whereBetween('start_at', [$startOfWeek, (clone $startOfWeek)->addDays(7)])
            ->whereIn('status', ['pendiente', 'confirmada'])
            ->get()
            ->keyBy(function ($a) {
                return $a->start_at->toIso8601String();
            });

        // Por cada disponibilidad declarada del médico, genera slots
        foreach ($doctor->availabilities as $avail) {
            $day = (clone $startOfWeek)->addDays($avail->weekday);
            
            // EXCLUIR SÁBADOS Y DOMINGOS (dayOfWeek: 0=domingo, 6=sábado)
            if ($day->dayOfWeek === 0 || $day->dayOfWeek === 6) {
                continue;
            }
            
            // Excluir fechas pasadas (comparar solo fechas, no horas)
            if ($day->toDateString() < Carbon::now()->toDateString()) {
                continue;
            }
            
            $start = Carbon::parse($day->toDateString() . ' ' . $avail->start_time);
            $end = Carbon::parse($day->toDateString() . ' ' . $avail->end_time);
            
            // Si es hoy, solo mostrar slots futuros
            if ($day->isToday() && $start->isPast()) {
                $start = Carbon::now()->minute(0)->second(0);
                // Redondear al próximo slot de 20 minutos
                $minutes = $start->minute;
                $nextSlot = (int)(ceil($minutes / $duration) * $duration);
                $start->minute($nextSlot);
            }

            while ($start->lt($end)) {
                $slotStart = $start->copy();
                $slotEnd = $start->copy()->addMinutes($duration);
                if ($slotEnd->gt($end)) {
                    break;
                }
                
                // Permitir reservas del día de hoy (cualquier horario futuro o actual)
                $canBook = $slotStart->isFuture() || 
                           $slotStart->isCurrentMinute() || 
                           ($slotStart->isToday() && $slotStart->gte(Carbon::now()));
                
                if ($canBook) {
                    $key = $slotStart->toIso8601String();
                    // Excluye slots que choquen con citas existentes
                    if (!$appointments->has($key)) {
                        $slots[] = [
                            'start' => $slotStart->toIso8601String(),
                            'end' => $slotEnd->toIso8601String(),
                            'doctor' => $doctor->slug,
                        ];
                    }
                }
                $start->addMinutes($duration);
            }
        }
        // Ordena por fecha de inicio
        usort($slots, fn($a, $b) => strcmp($a['start'], $b['start']));
        return $slots;
    }

    /**
     * Calcula disponibilidad en un rango de fechas arbitrario (para vista mensual).
     */
    protected function calcularDisponibilidadRango(Doctor $doctor, Carbon $startDate, Carbon $endDate, int $duration): array
    {
        $slots = [];
        
        // Obtener todas las citas en el rango
        $appointments = $doctor->appointments()
            ->whereBetween('start_at', [$startDate, $endDate])
            ->whereIn('status', ['pendiente', 'confirmada'])
            ->get()
            ->keyBy(function ($a) {
                return $a->start_at->toIso8601String();
            });

        // Iterar cada día del rango
        $currentDay = $startDate->copy();
        while ($currentDay->lte($endDate)) {
            // Excluir sábados y domingos
            if ($currentDay->dayOfWeek === 0 || $currentDay->dayOfWeek === 6) {
                $currentDay->addDay();
                continue;
            }
            
            // Excluir fechas pasadas (comparar solo fechas, no horas)
            if ($currentDay->toDateString() < Carbon::now()->toDateString()) {
                $currentDay->addDay();
                continue;
            }

            // Buscar disponibilidades del médico para este día de la semana (0=dom...6=sáb, pero Laravel usa 0=lun...6=dom)
            $weekday = $currentDay->dayOfWeek === 0 ? 6 : $currentDay->dayOfWeek - 1;
            
            foreach ($doctor->availabilities->where('weekday', $weekday) as $avail) {
                $start = Carbon::parse($currentDay->toDateString() . ' ' . $avail->start_time);
                $end = Carbon::parse($currentDay->toDateString() . ' ' . $avail->end_time);
                
                // Si es hoy, ajustar inicio
                if ($currentDay->isToday() && $start->isPast()) {
                    $start = Carbon::now()->minute(0)->second(0);
                    $minutes = $start->minute;
                    $nextSlot = (int)(ceil($minutes / $duration) * $duration);
                    $start->minute($nextSlot);
                }

                while ($start->lt($end)) {
                    $slotStart = $start->copy();
                    $slotEnd = $start->copy()->addMinutes($duration);
                    if ($slotEnd->gt($end)) {
                        break;
                    }
                    
                    // Permitir reservas del día de hoy (cualquier horario futuro o actual)
                    // Si es hoy, permitir desde ahora en adelante
                    // Si es fecha futura, permitir todos los horarios
                    $canBook = $slotStart->isFuture() || 
                               $slotStart->isCurrentMinute() || 
                               ($slotStart->isToday() && $slotStart->gte(Carbon::now()));
                    
                    if ($canBook) {
                        $key = $slotStart->toIso8601String();
                        if (!$appointments->has($key)) {
                            $slots[] = [
                                'start' => $slotStart->toIso8601String(),
                                'end' => $slotEnd->toIso8601String(),
                                'doctor' => $doctor->slug,
                            ];
                        }
                    }
                    $start->addMinutes($duration);
                }
            }
            
            $currentDay->addDay();
        }
        
        usort($slots, fn($a, $b) => strcmp($a['start'], $b['start']));
        return $slots;
    }
}
