<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <AdminHeader subtitle="Crear Médico" />
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <BackLink fallback="/admin/doctors" />
      <h1 class="text-3xl font-bold text-gray-900 mt-4 mb-6">Crear médico</h1>
      <form @submit.prevent="submit" class="bg-white rounded-xl shadow-lg p-6 space-y-6">
        <!-- Datos personales -->
        <div class="border-b pb-4">
          <h2 class="text-lg font-semibold mb-3 flex items-center gap-2"><i class="fas fa-user-md text-blue-600"></i>Datos personales</h2>
          <div class="space-y-3">
            <div>
              <label class="block text-sm font-medium mb-2">Nombre completo *</label>
              <input v-model="form.name" class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" required />
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">Especialidad *</label>
              <input v-model="form.specialty" class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" required />
            </div>
          </div>
        </div>

        <!-- Credenciales de acceso -->
        <div class="border-b pb-4">
          <h2 class="text-lg font-semibold mb-3 flex items-center gap-2"><i class="fas fa-key text-blue-600"></i>Credenciales de acceso</h2>
          <div class="space-y-3">
            <div>
              <label class="block text-sm font-medium mb-2">Correo electrónico *</label>
              <input v-model="form.email" type="email" class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" required />
              <p class="text-xs text-gray-500 mt-1">Se usará para iniciar sesión en el sistema</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">Contraseña *</label>
              <input v-model="form.password" type="password" class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" required minlength="8" />
              <p class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">Confirmar contraseña *</label>
              <input v-model="form.password_confirmation" type="password" class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" required minlength="8" />
            </div>
          </div>
        </div>

        <!-- Horario de disponibilidad -->
        <div class="pb-4">
          <h2 class="text-lg font-semibold mb-3 flex items-center gap-2"><i class="fas fa-calendar-alt text-blue-600"></i>Horario de disponibilidad</h2>
          <p class="text-sm text-gray-600 mb-3">Configura los días y horarios en que el médico atenderá pacientes</p>
          <div class="space-y-2">
            <div v-for="day in weekdays" :key="day.value" class="flex items-center gap-3 p-3 border-2 rounded-lg hover:border-blue-200 transition-colors">
              <input type="checkbox" v-model="form.availability[day.value].enabled" class="w-5 h-5 text-blue-600" />
              <label class="w-24 font-medium">{{ day.label }}</label>
              <div v-if="form.availability[day.value].enabled" class="flex items-center gap-2">
                <input type="time" v-model="form.availability[day.value].start" class="border-2 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" />
                <span>a</span>
                <input type="time" v-model="form.availability[day.value].end" class="border-2 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" />
              </div>
              <span v-else class="text-sm text-gray-400">No disponible</span>
            </div>
          </div>
        </div>

        <div class="flex gap-3 pt-4">
          <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
            <i class="fas fa-save"></i>
            Crear médico
          </button>
          <button type="button" @click="$inertia.visit(route('panel.doctors.index'))" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition-colors">
            Cancelar
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import BackLink from '../../Components/BackLink.vue'
import AdminHeader from '../../Components/AdminHeader.vue'

const weekdays = [
  { value: 0, label: 'Lunes' },
  { value: 1, label: 'Martes' },
  { value: 2, label: 'Miércoles' },
  { value: 3, label: 'Jueves' },
  { value: 4, label: 'Viernes' },
  { value: 5, label: 'Sábado' },
  { value: 6, label: 'Domingo' },
]

const form = ref({ 
  name: '', 
  email: '', 
  specialty: 'Neumología',
  password: '',
  password_confirmation: '',
  availability: {
    0: { enabled: true, start: '08:00', end: '17:00' },
    1: { enabled: true, start: '08:00', end: '17:00' },
    2: { enabled: true, start: '08:00', end: '17:00' },
    3: { enabled: true, start: '08:00', end: '17:00' },
    4: { enabled: true, start: '08:00', end: '17:00' },
    5: { enabled: false, start: '08:00', end: '13:00' },
    6: { enabled: false, start: '08:00', end: '13:00' },
  }
})

function submit() { 
  router.post(route('panel.doctors.store'), form.value) 
}
</script>
