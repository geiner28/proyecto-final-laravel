<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <!-- Header de navegación -->
    <AdminHeader :currentDoctor="doctor" subtitle="Calendario Semanal" />

    <!-- Calendario semanal del panel: muestra citas pendientes y confirmadas -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Navegación de semana -->
    <div class="bg-white rounded-xl shadow-md p-4 flex items-center justify-between">
      <button 
        @click="navegarSemana(-1)" 
        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition flex items-center gap-2"
      >
        <i class="fas fa-chevron-left"></i>
        Semana Anterior
      </button>
      <div class="text-center">
        <p class="text-sm text-gray-500">Semana del</p>
        <p class="font-bold text-lg text-gray-800">{{ formatearFecha(weekStart) }}</p>
      </div>
      <button 
        @click="navegarSemana(1)" 
        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition flex items-center gap-2"
      >
        Siguiente Semana
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>

    <!-- Resumen -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
        <p class="text-sm opacity-90">Total Citas</p>
        <p class="text-3xl font-bold mt-1">{{ appointments.length }}</p>
      </div>
      <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-6 text-white">
        <p class="text-sm opacity-90">Pendientes</p>
        <p class="text-3xl font-bold mt-1">{{ citasPendientes }}</p>
      </div>
      <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white">
        <p class="text-sm opacity-90">Confirmadas</p>
        <p class="text-3xl font-bold mt-1">{{ citasConfirmadas }}</p>
      </div>
    </div>

    <!-- Citas agrupadas por día -->
    <div v-if="citasPorDia.length === 0" class="bg-white rounded-xl shadow-md p-12 text-center">
      <i class="fas fa-calendar-times text-gray-300 text-6xl mb-4"></i>
      <h3 class="text-xl font-semibold text-gray-700 mb-2">No hay citas esta semana</h3>
      <p class="text-gray-500">Todas las citas aparecerán aquí una vez sean agendadas</p>
    </div>

    <div v-else class="space-y-6">
      <div v-for="dia in citasPorDia" :key="dia.fecha" class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
          <h3 class="text-xl font-bold text-white flex items-center gap-3">
            <i class="fas fa-calendar-day"></i>
            {{ dia.diaNombre }}
          </h3>
          <p class="text-sm text-blue-100 mt-1">{{ dia.fechaCompleta }}</p>
        </div>
        
        <div class="p-6 space-y-4">
          <div 
            v-for="cita in dia.citas" 
            :key="cita.slug" 
            class="border-l-4 rounded-lg p-4 hover:shadow-md transition"
            :class="{
              'border-yellow-500 bg-yellow-50': cita.status === 'pendiente',
              'border-green-500 bg-green-50': cita.status === 'confirmada',
              'border-red-500 bg-red-50': cita.status === 'rechazada'
            }"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                  <div class="flex items-center gap-2">
                    <i class="fas fa-clock text-gray-600"></i>
                    <span class="font-bold text-lg text-gray-900">{{ formatearHora(cita.start_at) }}</span>
                    <span class="text-gray-500">-</span>
                    <span class="text-gray-700">{{ formatearHora(cita.end_at) }}</span>
                  </div>
                  <span 
                    class="px-3 py-1 rounded-full text-xs font-semibold"
                    :class="{
                      'bg-yellow-200 text-yellow-800': cita.status === 'pendiente',
                      'bg-green-200 text-green-800': cita.status === 'confirmada',
                      'bg-red-200 text-red-800': cita.status === 'rechazada'
                    }"
                  >
                    {{ cita.status.toUpperCase() }}
                  </span>
                </div>
                
                <div class="space-y-1 text-sm">
                  <p class="flex items-center gap-2 text-gray-700">
                    <i class="fas fa-user w-4"></i>
                    <span class="font-medium">{{ cita.patient_name }}</span>
                  </p>
                  <p class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-envelope w-4"></i>
                    {{ cita.patient_email }}
                  </p>
                  <p v-if="cita.cedula_paciente" class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-id-card w-4"></i>
                    Cédula: {{ cita.cedula_paciente }}
                  </p>
                  <p v-if="cita.telefono_paciente" class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-phone w-4"></i>
                    {{ cita.telefono_paciente }}
                  </p>
                  <p v-if="cita.notes" class="flex items-start gap-2 text-gray-600 mt-2">
                    <i class="fas fa-comment w-4 mt-1"></i>
                    <span class="italic">{{ cita.notes }}</span>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { computed } from 'vue'
import AdminHeader from '../../Components/AdminHeader.vue'

const props = defineProps({
  doctor: Object,
  weekStart: String,
  appointments: Array,
})

// Contadores
const citasPendientes = computed(() => 
  props.appointments.filter(a => a.status === 'pendiente').length
)
const citasConfirmadas = computed(() => 
  props.appointments.filter(a => a.status === 'confirmada').length
)

// Agrupar citas por día
const citasPorDia = computed(() => {
  const grupos = {}
  
  props.appointments.forEach(cita => {
    const fecha = new Date(cita.start_at)
    const key = fecha.toISOString().split('T')[0]
    
    if (!grupos[key]) {
      grupos[key] = {
        fecha: key,
        diaNombre: fecha.toLocaleDateString('es-ES', { weekday: 'long' }),
        fechaCompleta: fecha.toLocaleDateString('es-ES', { 
          day: 'numeric', 
          month: 'long', 
          year: 'numeric' 
        }),
        citas: []
      }
    }
    
    grupos[key].citas.push(cita)
  })
  
  // Ordenar citas dentro de cada día por hora
  Object.values(grupos).forEach(dia => {
    dia.citas.sort((a, b) => new Date(a.start_at) - new Date(b.start_at))
  })
  
  // Convertir a array y ordenar por fecha
  return Object.values(grupos).sort((a, b) => a.fecha.localeCompare(b.fecha))
})

function navegarSemana(delta) {
  const fecha = new Date(props.weekStart)
  fecha.setDate(fecha.getDate() + (delta * 7))
  router.get('/calendar', { week: fecha.toISOString().slice(0,10) }, { preserveState: true })
}

function formatearFecha(fechaStr) {
  const fecha = new Date(fechaStr)
  return fecha.toLocaleDateString('es-ES', { 
    day: 'numeric', 
    month: 'long', 
    year: 'numeric' 
  })
}

function formatearHora(iso) {
  const d = new Date(iso)
  return d.toLocaleTimeString('es-ES', { 
    hour: '2-digit', 
    minute: '2-digit',
    hour12: false 
  })
}
</script>
