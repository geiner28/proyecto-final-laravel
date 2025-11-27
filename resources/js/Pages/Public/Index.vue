<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 relative overflow-x-hidden">
    <!-- Animated blobs background -->
    <div class="absolute top-20 -left-20 w-48 sm:w-72 h-48 sm:h-72 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob pointer-events-none"></div>
    <div class="absolute top-40 -right-20 w-48 sm:w-72 h-48 sm:h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob animation-delay-2000 pointer-events-none"></div>
    <div class="absolute bottom-20 left-1/2 w-48 sm:w-72 h-48 sm:h-72 bg-indigo-300 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob animation-delay-4000 pointer-events-none"></div>

    <!-- Navbar Glass -->
    <nav class="sticky top-0 z-50 glass border-b border-white/20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center transform hover:rotate-12 transition-transform duration-300">
              <i class="fas fa-heartbeat text-white text-xl"></i>
            </div>
            <h1 class="text-xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
              Explorar Médicos
            </h1>
          </div>
          <HomeButton />
        </div>
      </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 relative z-10">
      <!-- Hero Section -->
      <div class="text-center mb-8 sm:mb-12 fade-in-up px-4">
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 mb-3 sm:mb-4">
          Encuentra tu <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Especialista</span>
        </h2>
        <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto px-4">
          Selecciona un médico y agenda tu cita en minutos. Atención profesional cuando la necesites.
        </p>
      </div>

      <!-- Search and Filter Section -->
      <div class="mb-6 sm:mb-10 fade-in-up animation-delay-200">
        <div class="glass rounded-2xl p-4 sm:p-6 lg:p-8 shadow-xl">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            <!-- Search by name -->
            <div class="lg:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-search mr-2 text-blue-600"></i>
                Buscar por nombre
              </label>
              <div class="relative">
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Ej: Dr. García, María..."
                  class="w-full px-4 sm:px-5 py-2.5 sm:py-3 pl-10 sm:pl-12 bg-white/70 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm sm:text-base"
                />
                <i class="fas fa-search absolute left-3 sm:left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm sm:text-base"></i>
              </div>
            </div>

            <!-- Filter by specialty -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-filter mr-2 text-purple-600"></i>
                Filtrar por especialidad
              </label>
              <select
                v-model="selectedSpecialty"
                class="w-full px-4 sm:px-5 py-2.5 sm:py-3 bg-white/70 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 cursor-pointer text-sm sm:text-base"
              >
                <option value="">Todas las especialidades</option>
                <option v-for="specialty in specialties" :key="specialty" :value="specialty">
                  {{ specialty }}
                </option>
              </select>
            </div>
          </div>

          <!-- Active Filters -->
          <div v-if="searchQuery || selectedSpecialty" class="mt-4 sm:mt-6 flex flex-wrap gap-2">
            <span class="text-xs sm:text-sm text-gray-600 font-medium">Filtros activos:</span>
            <span v-if="searchQuery" class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs sm:text-sm font-medium">
              Búsqueda: "{{ searchQuery }}"
              <button @click="searchQuery = ''" class="hover:bg-blue-200 rounded-full p-0.5">
                <i class="fas fa-times text-xs"></i>
              </button>
            </span>
            <span v-if="selectedSpecialty" class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs sm:text-sm font-medium">
              {{ selectedSpecialty }}
              <button @click="selectedSpecialty = ''" class="hover:bg-purple-200 rounded-full p-0.5">
                <i class="fas fa-times text-xs"></i>
              </button>
            </span>
          </div>
        </div>
      </div>

      <!-- Results count -->
      <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 fade-in-up animation-delay-400">
        <p class="text-sm sm:text-base text-gray-600">
          <span class="font-bold text-gray-900">{{ filteredDoctors.length }}</span> 
          {{ filteredDoctors.length === 1 ? 'médico encontrado' : 'médicos encontrados' }}
        </p>
        <div class="flex gap-2">
          <button
            @click="viewMode = 'grid'"
            :class="[
              'p-2 rounded-lg transition-all text-sm sm:text-base',
              viewMode === 'grid' ? 'bg-blue-100 text-blue-600' : 'bg-white/50 text-gray-500 hover:bg-white/80'
            ]"
          >
            <i class="fas fa-th"></i>
          </button>
          <button
            @click="viewMode = 'list'"
            :class="[
              'p-2 rounded-lg transition-all text-sm sm:text-base',
              viewMode === 'list' ? 'bg-blue-100 text-blue-600' : 'bg-white/50 text-gray-500 hover:bg-white/80'
            ]"
          >
            <i class="fas fa-list"></i>
          </button>
        </div>
      </div>

      <!-- Empty state -->
      <div v-if="filteredDoctors.length === 0" class="text-center py-12 sm:py-20 fade-in-up">
        <div class="glass rounded-3xl p-8 sm:p-12 inline-block">
          <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-4 sm:mb-6 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full flex items-center justify-center">
            <i class="fas fa-user-md text-3xl sm:text-4xl text-blue-600"></i>
          </div>
          <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">No se encontraron médicos</h3>
          <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">Intenta ajustar los filtros de búsqueda</p>
          <button
            @click="clearFilters"
            class="px-5 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 text-sm sm:text-base"
          >
            <i class="fas fa-redo mr-2"></i>
            Limpiar filtros
          </button>
        </div>
      </div>

      <!-- Doctors Grid View -->
      <div v-else-if="viewMode === 'grid'" class="grid gap-4 sm:gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 fade-in-up animation-delay-600">
        <div
          v-for="(doctor, index) in filteredDoctors"
          :key="doctor.slug"
          class="group glass rounded-2xl overflow-hidden hover:shadow-2xl transition-all duration-300 cursor-pointer transform hover:-translate-y-2"
          :class="`fade-in-up animation-delay-${(index % 3) * 200}`"
          @click="verCalendario(doctor.slug)"
        >
          <!-- Doctor Avatar/Header -->
          <div class="h-32 bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-500 relative overflow-hidden">
            <div class="absolute inset-0 bg-grid-white/10"></div>
            <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2">
              <div class="w-20 h-20 rounded-full bg-white shadow-xl flex items-center justify-center ring-4 ring-white/50 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-user-md text-3xl bg-gradient-to-br from-blue-600 to-purple-600 bg-clip-text text-transparent"></i>
              </div>
            </div>
          </div>

          <!-- Doctor Info -->
          <div class="pt-12 sm:pt-14 pb-5 sm:pb-6 px-4 sm:px-6 space-y-3 sm:space-y-4">
            <div class="text-center">
              <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors break-words">
                {{ doctor.name }}
              </h3>
              <span class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1 sm:py-1.5 rounded-full bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 font-semibold text-xs sm:text-sm">
                <i class="fas fa-stethoscope text-xs"></i>
                {{ doctor.specialty }}
              </span>
            </div>

            <div class="space-y-2 text-xs sm:text-sm text-gray-600">
              <div class="flex items-center gap-2">
                <i class="fas fa-envelope text-blue-500 w-4 flex-shrink-0"></i>
                <span class="truncate">{{ doctor.email }}</span>
              </div>
              <div class="flex items-center gap-2">
                <i class="fas fa-clock text-green-500 w-4 flex-shrink-0"></i>
                <span>Disponibilidad inmediata</span>
              </div>
            </div>

            <button class="w-full mt-3 sm:mt-4 px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-105 transition-all duration-200 relative overflow-hidden group text-sm sm:text-base">
              <span class="relative z-10 flex items-center justify-center gap-2">
                <i class="fas fa-calendar-check"></i>
                Ver Calendario
              </span>
              <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
            </button>
          </div>
        </div>
      </div>

      <!-- Doctors List View -->
      <div v-else class="space-y-3 sm:space-y-4 fade-in-up animation-delay-600">
        <div
          v-for="(doctor, index) in filteredDoctors"
          :key="doctor.slug"
          class="group glass rounded-2xl p-4 sm:p-6 hover:shadow-2xl transition-all duration-300 cursor-pointer"
          :class="`fade-in-right animation-delay-${index * 100}`"
          @click="verCalendario(doctor.slug)"
        >
          <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-6">
            <!-- Avatar -->
            <div class="flex-shrink-0 mx-auto sm:mx-0">
              <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-user-md text-xl sm:text-2xl text-white"></i>
              </div>
            </div>

            <!-- Info -->
            <div class="flex-1 min-w-0 text-center sm:text-left">
              <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors break-words">
                {{ doctor.name }}
              </h3>
              <div class="flex flex-wrap gap-2 sm:gap-3 text-xs sm:text-sm text-gray-600 justify-center sm:justify-start">
                <span class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1 rounded-full bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 font-semibold">
                  <i class="fas fa-stethoscope"></i>
                  {{ doctor.specialty }}
                </span>
                <span class="flex items-center gap-1.5 sm:gap-2">
                  <i class="fas fa-envelope text-blue-500 flex-shrink-0"></i>
                  <span class="truncate">{{ doctor.email }}</span>
                </span>
                <span class="flex items-center gap-1.5 sm:gap-2">
                  <i class="fas fa-clock text-green-500"></i>
                  Disponible ahora
                </span>
              </div>
            </div>

            <!-- Action -->
            <div class="flex-shrink-0 w-full sm:w-auto">
              <button class="w-full sm:w-auto px-6 sm:px-8 py-2.5 sm:py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-105 transition-all duration-200 relative overflow-hidden group text-sm sm:text-base">
                <span class="relative z-10 flex items-center justify-center gap-2">
                  <i class="fas fa-calendar-check"></i>
                  Ver Calendario
                </span>
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Floating Stats -->
      <div class="mt-12 sm:mt-16 mb-8 sm:mb-12 grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 fade-in-up animation-delay-800">
        <div class="glass rounded-2xl p-5 sm:p-6 text-center transform hover:scale-105 transition-all duration-300">
          <div class="w-12 h-12 sm:w-14 sm:h-14 mx-auto mb-3 sm:mb-4 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center">
            <i class="fas fa-user-md text-xl sm:text-2xl text-white"></i>
          </div>
          <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ doctors.length }}</p>
          <p class="text-xs sm:text-sm text-gray-600 font-medium">Especialistas</p>
        </div>

        <div class="glass rounded-2xl p-5 sm:p-6 text-center transform hover:scale-105 transition-all duration-300">
          <div class="w-12 h-12 sm:w-14 sm:h-14 mx-auto mb-3 sm:mb-4 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center">
            <i class="fas fa-clock text-xl sm:text-2xl text-white"></i>
          </div>
          <p class="text-2xl sm:text-3xl font-bold text-gray-900">&lt;2min</p>
          <p class="text-xs sm:text-sm text-gray-600 font-medium">Tiempo de reserva</p>
        </div>

        <div class="glass rounded-2xl p-5 sm:p-6 text-center transform hover:scale-105 transition-all duration-300">
          <div class="w-12 h-12 sm:w-14 sm:h-14 mx-auto mb-3 sm:mb-4 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center">
            <i class="fas fa-star text-xl sm:text-2xl text-white"></i>
          </div>
          <p class="text-2xl sm:text-3xl font-bold text-gray-900">100%</p>
          <p class="text-xs sm:text-sm text-gray-600 font-medium">Satisfacción</p>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gradient-to-br from-gray-900 to-gray-800 text-white py-8 sm:py-12 px-4 sm:px-6 relative overflow-hidden mt-8 sm:mt-16">
      <!-- Elementos decorativos -->
      <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
      <div class="absolute -top-20 -right-20 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
      <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
      
      <div class="max-w-7xl mx-auto relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 mb-6 sm:mb-8">
          <div>
            <div class="flex items-center gap-3 mb-4">
              <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center">
                <i class="fas fa-heartbeat text-white"></i>
              </div>
              <h3 class="font-bold text-lg sm:text-xl">MediCitas</h3>
            </div>
            <p class="text-gray-400 text-xs sm:text-sm leading-relaxed">Sistema integral de gestión de citas médicas para mejorar la atención al paciente y optimizar el trabajo de los profesionales.</p>
          </div>
          
          <div>
            <h4 class="font-semibold mb-3 sm:mb-4 text-base sm:text-lg">Enlaces Rápidos</h4>
            <ul class="space-y-2 sm:space-y-3 text-xs sm:text-sm text-gray-400">
              <li><a href="/#servicios" class="hover:text-white transition-all duration-300 hover:translate-x-1 inline-block">Servicios</a></li>
              <li><Link :href="route('public.consultar.form')" class="hover:text-white transition-all duration-300 hover:translate-x-1 inline-block">Consultar Cita</Link></li>
              <li><a href="/#como-funciona" class="hover:text-white transition-all duration-300 hover:translate-x-1 inline-block">Cómo Funciona</a></li>
            </ul>
          </div>
          
          <div>
            <h4 class="font-semibold mb-3 sm:mb-4 text-base sm:text-lg">Contacto</h4>
            <div class="space-y-2 sm:space-y-3 text-xs sm:text-sm text-gray-400">
              <p class="flex items-center gap-2">
                <i class="fas fa-envelope text-blue-400 flex-shrink-0"></i>
                <span class="break-all">contacto@medicitas.com</span>
              </p>
              <p class="flex items-center gap-2">
                <i class="fas fa-phone text-green-400 flex-shrink-0"></i>
                +593 99 999 9999
              </p>
              <p class="flex items-center gap-2">
                <i class="fas fa-map-marker-alt text-red-400 flex-shrink-0"></i>
                Quito, Ecuador
              </p>
            </div>
          </div>
        </div>
        
        <div class="border-t border-gray-800 pt-4 sm:pt-6 text-center text-xs sm:text-sm text-gray-400">
          &copy; {{ new Date().getFullYear() }} MediCitas - Sistema de Citas Médicas. Todos los derechos reservados.
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
// Página de exploración: Lista de médicos con búsqueda y filtros
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import HomeButton from '../../Components/HomeButton.vue'

const props = defineProps({
  doctors: Array,
})

const searchQuery = ref('')
const selectedSpecialty = ref('')
const viewMode = ref('grid') // 'grid' or 'list'

// Get unique specialties
const specialties = computed(() => {
  const uniqueSpecialties = [...new Set(props.doctors.map(d => d.specialty))]
  return uniqueSpecialties.sort()
})

// Filter doctors based on search and specialty
const filteredDoctors = computed(() => {
  let result = props.doctors

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(doctor =>
      doctor.name.toLowerCase().includes(query) ||
      doctor.email.toLowerCase().includes(query)
    )
  }

  // Filter by specialty
  if (selectedSpecialty.value) {
    result = result.filter(doctor => doctor.specialty === selectedSpecialty.value)
  }

  return result
})

function verCalendario(doctorSlug) {
  router.get(`/doctors/${doctorSlug}`)
}

function clearFilters() {
  searchQuery.value = ''
  selectedSpecialty.value = ''
}
</script>

<style scoped>
.glass {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(16px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.3);
}

@keyframes blob {
  0%, 100% {
    transform: translate(0, 0) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-200 {
  animation-delay: 0.2s;
}

.animation-delay-400 {
  animation-delay: 0.4s;
}

.animation-delay-600 {
  animation-delay: 0.6s;
}

.animation-delay-800 {
  animation-delay: 0.8s;
}

.animation-delay-1000 {
  animation-delay: 1s;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}

@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fade-in-right {
  from {
    opacity: 0;
    transform: translateX(-30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.fade-in-up {
  animation: fade-in-up 0.6s ease-out forwards;
}

.fade-in-right {
  animation: fade-in-right 0.5s ease-out forwards;
}

.bg-grid-white\/10 {
  background-image: linear-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
  background-size: 20px 20px;
}
</style>
