<template>
  <div class="p-6 space-y-4">
    <HomeButton />
    <BackLink fallback="/admin/doctors" />
    <h1 class="text-2xl font-bold">Editar médico</h1>
    <form @submit.prevent="submit" class="max-w-md space-y-4">
      <!-- Datos personales -->
      <div class="border-b pb-4">
        <h2 class="text-lg font-semibold mb-3">Datos personales</h2>
        <div class="space-y-3">
          <div>
            <label class="block text-sm font-medium">Nombre completo *</label>
            <input v-model="form.name" class="mt-1 w-full border rounded p-2" required />
          </div>
          <div>
            <label class="block text-sm font-medium">Especialidad *</label>
            <input v-model="form.specialty" class="mt-1 w-full border rounded p-2" required />
          </div>
        </div>
      </div>

      <!-- Credenciales de acceso -->
      <div class="border-b pb-4">
        <h2 class="text-lg font-semibold mb-3">Credenciales de acceso</h2>
        <div class="space-y-3">
          <div>
            <label class="block text-sm font-medium">Correo electrónico *</label>
            <input v-model="form.email" type="email" class="mt-1 w-full border rounded p-2" required />
            <p class="text-xs text-gray-500 mt-1">Se usará para iniciar sesión en el sistema</p>
          </div>
        </div>
      </div>

      <!-- Cambiar contraseña (opcional) -->
      <div class="border-b pb-4">
        <h2 class="text-lg font-semibold mb-3">Cambiar contraseña</h2>
        <p class="text-sm text-gray-600 mb-3">Deja en blanco si no deseas cambiar la contraseña</p>
        <div class="space-y-3">
          <div>
            <label class="block text-sm font-medium">Nueva contraseña</label>
            <input v-model="form.password" type="password" class="mt-1 w-full border rounded p-2" minlength="8" />
            <p class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres</p>
          </div>
          <div>
            <label class="block text-sm font-medium">Confirmar nueva contraseña</label>
            <input v-model="form.password_confirmation" type="password" class="mt-1 w-full border rounded p-2" minlength="8" />
          </div>
        </div>
      </div>

      <!-- Horario de disponibilidad -->
      <div class="pb-4">
        <h2 class="text-lg font-semibold mb-3">Horario de disponibilidad</h2>
        <p class="text-sm text-gray-600 mb-3">Configura los días y horarios en que el médico atenderá pacientes</p>
        <div class="space-y-2">
          <div v-for="day in weekdays" :key="day.value" class="flex items-center gap-3 p-3 border rounded">
            <input type="checkbox" v-model="form.availability[day.value].enabled" class="w-5 h-5" />
            <label class="w-24 font-medium">{{ day.label }}</label>
            <div v-if="form.availability[day.value].enabled" class="flex items-center gap-2">
              <input type="time" v-model="form.availability[day.value].start" class="border rounded px-2 py-1 text-sm" />
              <span>a</span>
              <input type="time" v-model="form.availability[day.value].end" class="border rounded px-2 py-1 text-sm" />
            </div>
            <span v-else class="text-sm text-gray-400">No disponible</span>
          </div>
        </div>
      </div>

      <div class="flex gap-3">
        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors">Actualizar médico</button>
        <button type="button" @click="$inertia.visit(route('panel.doctors.index'))" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded transition-colors">Cancelar</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import BackLink from '../../Components/BackLink.vue'
import HomeButton from '../../Components/HomeButton.vue'

const props = defineProps({ doctor: Object })

const weekdays = [
  { value: 0, label: 'Lunes' },
  { value: 1, label: 'Martes' },
  { value: 2, label: 'Miércoles' },
  { value: 3, label: 'Jueves' },
  { value: 4, label: 'Viernes' },
  { value: 5, label: 'Sábado' },
  { value: 6, label: 'Domingo' }
]

// Transform existing availabilities into form structure
const availabilityData = {}
weekdays.forEach(day => {
  const existing = props.doctor.availabilities?.find(a => a.weekday === day.value)
  availabilityData[day.value] = existing 
    ? { enabled: true, start: existing.start_time, end: existing.end_time }
    : { enabled: false, start: '08:00', end: '17:00' }
})

const form = ref({ 
  name: props.doctor.name, 
  email: props.doctor.email, 
  specialty: props.doctor.specialty,
  password: '',
  password_confirmation: '',
  availability: availabilityData
})

function submit() { 
  router.put(route('panel.doctors.update', props.doctor.slug), form.value) 
}
</script>
