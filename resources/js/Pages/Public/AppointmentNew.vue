<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <div class="max-w-4xl mx-auto px-6 py-10 space-y-8">
      <div class="flex items-start justify-between">
        <div class="space-y-1">
          <h1 class="text-3xl font-extrabold text-gray-800">Reservar cita</h1>
          <p class="text-sm text-gray-500">Completa tus datos para confirmar la solicitud.</p>
        </div>
        <div class="flex gap-2">
          <HomeButton />
          <BackLink fallback="/explorar" />
        </div>
      </div>

      <!-- Calendario para cambiar horario si lo desea -->
      <PatientCalendar v-if="availability" :availability="availability" :initial="null" :duration="duration" @select="onSelect" />

      
      <div class="rounded-2xl border bg-white/80 backdrop-blur p-6 shadow-sm flex flex-col md:flex-row gap-6">
        <div class="flex-1 space-y-2">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-600">Profesional</h2>
          <p class="text-lg font-medium text-gray-800">{{ doctor.name }}</p>
          <p class="text-xs text-blue-700 font-semibold inline-block bg-blue-100 rounded px-2 py-0.5">{{ doctor.specialty }}</p>
        </div>
        <div class="flex-1 space-y-2">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-600">Horario Seleccionado</h2>
            <p class="text-lg font-medium text-gray-800">{{ formatear(startLocal) }}</p>
            <p class="text-xs text-gray-500">Duración estimada: {{ duration }} min</p>
        </div>
      </div>

      <!-- Formulario -->
      <form @submit.prevent="submit" class="rounded-2xl border bg-white/90 backdrop-blur p-6 shadow-sm space-y-5 max-w-xl">
        <!-- Mensajes de error -->
        <div v-if="hasErrors" class="bg-red-50 border border-red-200 rounded-lg p-4 space-y-2">
          <p v-if="$page.props.errors.start" class="text-sm text-red-600 flex items-start gap-2">
            <i class="fas fa-exclamation-circle mt-0.5"></i>
            {{ $page.props.errors.start }}
          </p>
          <p v-if="$page.props.errors.cedula_paciente" class="text-sm text-red-600 flex items-start gap-2">
            <i class="fas fa-exclamation-circle mt-0.5"></i>
            {{ $page.props.errors.cedula_paciente }}
          </p>
        </div>
        
        <div class="space-y-1">
          <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600">Nombre del paciente *</label>
          <input v-model="form.patient_name" class="w-full rounded-lg border px-3 py-2 text-sm focus:ring-blue-400 focus:border-blue-400" required />
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600">Correo electrónico *</label>
          <input v-model="form.patient_email" type="email" class="w-full rounded-lg border px-3 py-2 text-sm focus:ring-blue-400 focus:border-blue-400" required />
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600">Cédula / Documento de Identidad *</label>
          <input v-model="form.cedula_paciente" class="w-full rounded-lg border px-3 py-2 text-sm focus:ring-blue-400 focus:border-blue-400" placeholder="Ej: 1234567890" required />
          <p class="text-xs text-gray-500 mt-1">Podrás consultar tu cita con este número</p>
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600">Teléfono (opcional)</label>
          <input v-model="form.telefono_paciente" type="tel" class="w-full rounded-lg border px-3 py-2 text-sm focus:ring-blue-400 focus:border-blue-400" placeholder="Ej: 0987654321" />
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600">Notas (opcional)</label>
          <textarea v-model="form.notes" rows="3" class="w-full rounded-lg border px-3 py-2 text-sm focus:ring-blue-400 focus:border-blue-400" placeholder="Motivo de la consulta, síntomas, etc." />
        </div>
        <button class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
          <i class="fas fa-check-circle"></i>
          Confirmar reserva
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
// Comentarios: Envia POST a /appointments creando cita en estado pendiente.
import { router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import BackLink from '../../Components/BackLink.vue'
import HomeButton from '../../Components/HomeButton.vue'
import PatientCalendar from '../../Components/PatientCalendar.vue'

const props = defineProps({
  doctor: Object,
  start: String,
  duration: Number,
  availability: { type: Array, default: () => [] },
})

const page = usePage()
const hasErrors = computed(() => Object.keys(page.props.errors || {}).length > 0)

const form = ref({
  patient_name: '',
  patient_email: '',
  cedula_paciente: '',
  telefono_paciente: '',
  notes: '',
})

// Local selected start so the calendar can update it interactively
const startLocal = ref(props.start)

function onSelect(iso) {
  startLocal.value = iso
}

function submit() {
  router.post('/appointments', { doctor: props.doctor.slug, start: startLocal.value, ...form.value })
}

function formatear(iso) {
  const d = new Date(iso)
  return d.toLocaleString()
}
</script>
