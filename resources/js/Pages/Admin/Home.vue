<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <!-- Header de navegación -->
    <AdminHeader :currentDoctor="currentDoctor" />
    <!-- Contenido principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Hero Section -->
      <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-2xl overflow-hidden mb-8">
        <div class="px-8 py-10 sm:px-12 sm:py-14 text-white">
          <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex-1">
              <h2 class="text-3xl sm:text-4xl font-extrabold mb-3">
                {{ currentDoctor ? `Bienvenido, Dr. ${currentDoctor.name.split(' ')[0]}` : 'Panel de Administración' }}
              </h2>
              <p class="text-blue-100 text-lg">
                {{ currentDoctor ? 'Gestiona tus citas y horarios de atención' : 'Supervisa todas las citas y médicos del sistema' }}
              </p>
            </div>
            <div class="hidden sm:block">
              <i class="fas fa-calendar-check text-blue-200 opacity-20" style="font-size: 120px;"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Filtro de médico (solo admin) -->
      <div v-if="!currentDoctor" class="bg-white rounded-xl shadow-md p-6 mb-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-end gap-4">
          <div class="flex-1 w-full">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-filter mr-2 text-blue-600"></i>
              Filtrar citas por médico
            </label>
            <select 
              class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" 
              v-model="doctorSlug" 
              @change="filtrar"
            >
              <option value="">📋 Todos los médicos</option>
              <option v-for="d in doctors" :key="d.slug" :value="d.slug">
                👨‍⚕️ {{ d.name }} - {{ d.specialty }}
              </option>
            </select>
          </div>
          <button
            @click="verCalendario"
            :disabled="!doctorSlug"
            class="px-6 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            :class="doctorSlug 
              ? 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5' 
              : 'bg-gray-200 text-gray-500 cursor-not-allowed'"
          >
            <i class="fas fa-calendar-week"></i>
            Ver Calendario
          </button>
        </div>
      </div>

      <!-- Botón de calendario para médicos -->
      <div v-else class="bg-white rounded-xl shadow-md p-6 mb-8 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-stethoscope text-blue-600 text-xl"></i>
          </div>
          <div>
            <p class="text-sm text-gray-500">Especialidad</p>
            <p class="font-semibold text-gray-800">{{ currentDoctor.specialty }}</p>
          </div>
        </div>
        <button
          @click="verCalendario"
          class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center gap-2"
        >
          <i class="fas fa-calendar-alt"></i>
          Ver Mi Calendario
        </button>
      </div>

      <!-- Estadísticas Rápidas -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500 hover:shadow-xl transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500 font-medium">Pendientes</p>
              <p class="text-3xl font-bold text-gray-900 mt-1">{{ pending.length }}</p>
            </div>
            <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center">
              <i class="fas fa-clock text-yellow-600 text-2xl"></i>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500 hover:shadow-xl transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500 font-medium">Próximas</p>
              <p class="text-3xl font-bold text-gray-900 mt-1">{{ upcoming.length }}</p>
            </div>
            <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
              <i class="fas fa-check-circle text-green-600 text-2xl"></i>
            </div>
          </div>
        </div>

        <div v-if="!currentDoctor" class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500 hover:shadow-xl transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500 font-medium">Médicos</p>
              <p class="text-3xl font-bold text-gray-900 mt-1">{{ doctors.length }}</p>
            </div>
            <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
              <i class="fas fa-user-md text-blue-600 text-2xl"></i>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500 hover:shadow-xl transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Citas</p>
              <p class="text-3xl font-bold text-gray-900 mt-1">{{ pending.length + upcoming.length }}</p>
            </div>
            <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center">
              <i class="fas fa-calendar-check text-purple-600 text-2xl"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Citas Pendientes y Próximas -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Citas Pendientes -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
          <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
              <i class="fas fa-exclamation-circle"></i>
              Citas Pendientes
            </h2>
            <p class="text-yellow-100 text-sm mt-1">Requieren tu atención</p>
          </div>
          
          <div class="p-6">
            <div v-if="pending.length === 0" class="text-center py-12">
              <i class="fas fa-check-double text-gray-300 text-5xl mb-4"></i>
              <p class="text-gray-500 font-medium">¡Todo al día!</p>
              <p class="text-gray-400 text-sm mt-1">No hay citas pendientes</p>
            </div>
            
            <div v-else class="space-y-4 max-h-96 overflow-y-auto">
              <div 
                v-for="a in pending" 
                :key="a.slug" 
                class="border-l-4 border-yellow-500 bg-yellow-50 rounded-lg p-4 hover:shadow-md transition-all"
              >
                <div class="flex items-start justify-between gap-4">
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                      <i class="fas fa-user text-gray-600"></i>
                      <span class="font-bold text-gray-900">{{ a.patient_name }}</span>
                    </div>
                    <div class="text-sm text-gray-600 space-y-1">
                      <div class="flex items-center gap-2">
                        <i class="fas fa-calendar text-blue-500"></i>
                        <span>{{ formatear(a.start_at) }}</span>
                      </div>
                      <div v-if="!currentDoctor" class="flex items-center gap-2">
                        <i class="fas fa-stethoscope text-green-500"></i>
                        <span>{{ a.doctor.name }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="flex flex-col gap-2">
                    <button 
                      class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2 text-sm whitespace-nowrap" 
                      @click="aceptar(a.slug)"
                    >
                      <i class="fas fa-check"></i>
                      Aceptar
                    </button>
                    <button 
                      class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2 text-sm whitespace-nowrap" 
                      @click="rechazar(a.slug)"
                    >
                      <i class="fas fa-times"></i>
                      Rechazar
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Próximas Confirmadas -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
          <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
              <i class="fas fa-calendar-check"></i>
              Próximas Confirmadas
            </h2>
            <p class="text-green-100 text-sm mt-1">Citas programadas</p>
          </div>
          
          <div class="p-6">
            <div v-if="upcoming.length === 0" class="text-center py-12">
              <i class="fas fa-calendar-times text-gray-300 text-5xl mb-4"></i>
              <p class="text-gray-500 font-medium">Sin citas próximas</p>
              <p class="text-gray-400 text-sm mt-1">No hay citas confirmadas</p>
            </div>
            
            <div v-else class="space-y-3 max-h-96 overflow-y-auto">
              <div 
                v-for="a in upcoming" 
                :key="a.slug" 
                class="border-l-4 border-green-500 bg-green-50 rounded-lg p-4 hover:shadow-md transition-all"
              >
                <div class="flex items-center gap-2 mb-2">
                  <i class="fas fa-user text-gray-600"></i>
                  <span class="font-bold text-gray-900">{{ a.patient_name }}</span>
                </div>
                <div class="text-sm text-gray-600 space-y-1">
                  <div class="flex items-center gap-2">
                    <i class="fas fa-calendar text-blue-500"></i>
                    <span>{{ formatear(a.start_at) }}</span>
                  </div>
                  <div v-if="!currentDoctor" class="flex items-center gap-2">
                    <i class="fas fa-stethoscope text-green-500"></i>
                    <span>{{ a.doctor.name }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    
      <!-- Acceso rápido calendario por médico (solo para admin) -->
      <div v-if="!currentDoctor" class="mt-8 bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
              <i class="fas fa-hospital-user text-blue-600"></i>
              Calendario por Médico
            </h2>
            <p class="text-gray-500 mt-1">Acceso rápido a los calendarios individuales</p>
          </div>
        </div>
        
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
          <div 
            v-for="d in doctors" 
            :key="d.slug" 
            class="group border-2 border-gray-200 rounded-xl p-5 bg-gradient-to-br from-white to-gray-50 hover:border-blue-500 hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:-translate-y-1"
            @click="router.get('/calendar', { doctor: d.slug })"
          >
            <div class="flex items-start gap-4">
              <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0 group-hover:scale-110 transition-transform">
                {{ d.name.charAt(0) }}
              </div>
              <div class="flex-1 min-w-0">
                <h3 class="font-bold text-gray-900 truncate group-hover:text-blue-600 transition-colors">{{ d.name }}</h3>
                <p class="text-sm text-gray-500 truncate mt-1">{{ d.specialty }}</p>
                <div class="mt-3 flex items-center gap-2 text-xs text-blue-600 font-medium group-hover:gap-3 transition-all">
                  <span>Ver calendario</span>
                  <i class="fas fa-arrow-right"></i>
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
import { ref } from 'vue'
import AdminHeader from '../../Components/AdminHeader.vue'

const props = defineProps({
  pending: Array,
  upcoming: Array,
  doctors: Array,
  currentDoctor: Object,
})

const doctorSlug = ref('')

function filtrar() {
  if (props.currentDoctor) return
  router.get('/home', { doctor: doctorSlug.value }, { preserveState: true })
}

function verCalendario() {
  if (props.currentDoctor) {
    router.get('/calendar')
  } else if (doctorSlug.value) {
    router.get('/calendar', { doctor: doctorSlug.value })
  }
}

function aceptar(slug) {
  router.post(`/admin/appointments/${slug}/accept`)
}

function rechazar(slug) {
  router.post(`/admin/appointments/${slug}/reject`)
}

function formatear(iso) {
  const d = new Date(iso)
  return d.toLocaleString('es-ES', { 
    weekday: 'short',
    day: 'numeric', 
    month: 'short',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>
