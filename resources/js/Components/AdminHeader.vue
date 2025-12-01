<template>
  <nav class="bg-white shadow-lg border-b-4 border-blue-600 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo y título -->
        <div class="flex items-center gap-3">
          <Link :href="homeRoute" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
            <div class="bg-gradient-to-br from-blue-600 to-indigo-600 w-10 h-10 rounded-lg flex items-center justify-center">
              <i class="fas fa-heartbeat text-white text-xl"></i>
            </div>
            <div>
              <h1 class="text-xl font-bold text-gray-900">MediCitas</h1>
              <p class="text-xs text-gray-500">{{ subtitle || (currentDoctor ? 'Panel Médico' : 'Panel Administrativo') }}</p>
            </div>
          </Link>
        </div>

        <!-- Navegación -->
        <div class="flex items-center gap-3">
          <!-- Botón Home -->
          <Link :href="homeRoute" 
             class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
             title="Inicio">
            <i class="fas fa-home text-lg"></i>
            <span class="hidden lg:inline text-sm font-medium">{{ currentDoctor ? 'Dashboard' : 'Inicio' }}</span>
          </Link>

          <!-- Gestión de Citas -->
          <a href="/admin/appointments" 
             class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
             title="Gestión de Citas">
            <i class="fas fa-calendar-check text-lg"></i>
            <span class="hidden lg:inline text-sm font-medium">Citas</span>
          </a>

          <!-- Gestionar Médicos (solo admin) -->
          <a v-if="!currentDoctor" 
             :href="route('panel.doctors.index')" 
             class="flex items-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors shadow-sm"
             title="Gestión de Médicos">
            <i class="fas fa-user-md"></i>
            <span class="hidden md:inline text-sm">Médicos</span>
          </a>

          <!-- Perfil de usuario -->
          <div class="flex items-center gap-3 px-3 py-2 bg-gray-50 rounded-lg ml-2">
            <div class="hidden sm:block text-right">
              <p class="text-sm font-semibold text-gray-900">{{ userName }}</p>
              <p class="text-xs text-gray-500">{{ userRole }}</p>
            </div>
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
              {{ userName.charAt(0) }}
            </div>
          </div>

          <!-- Botón de cerrar sesión -->
          <form @submit.prevent="logout" class="ml-2">
            <button 
              type="submit"
              class="group flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-red-50 hover:border-red-400 transition-all duration-200 text-gray-700 hover:text-red-600 font-medium"
              title="Cerrar sesión"
            >
              <i class="fas fa-sign-out-alt transition-colors"></i>
              <span class="hidden lg:inline text-sm">Salir</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { router, Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  currentDoctor: {
    type: Object,
    default: null
  },
  subtitle: {
    type: String,
    default: null
  }
})

const homeRoute = computed(() => {
  // Si es médico, ir a su dashboard
  if (props.currentDoctor) {
    return '/doctor/appointments'
  }
  // Si es admin, ir al panel home
  return '/home'
})

const userName = computed(() => {
  return props.currentDoctor ? props.currentDoctor.name : 'Administrador'
})

const userRole = computed(() => {
  return props.currentDoctor ? props.currentDoctor.specialty : 'Admin'
})

function logout() {
  router.post('/logout')
}
</script>
