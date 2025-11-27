<template>
  <div class="p-6 space-y-4">
    <HomeButton />
    <BackLink fallback="/admin/appointments" />
    <h1 class="text-2xl font-bold">Crear cita (panel)</h1>
    
    <!-- Mensajes de error -->
    <div v-if="hasErrors" class="max-w-md bg-red-50 border border-red-200 rounded-lg p-4 space-y-2">
      <p v-for="(error, key) in $page.props.errors" :key="key" class="text-sm text-red-600 flex items-start gap-2">
        <i class="fas fa-exclamation-circle mt-0.5"></i>
        {{ error }}
      </p>
    </div>
    
    <form @submit.prevent="submit" class="max-w-md space-y-3">
      <div>
        <label class="block text-sm">Médico</label>
        <select v-model="form.doctor_id" class="mt-1 w-full border rounded p-2" required>
          <option v-for="d in doctors" :key="d.id" :value="d.id">{{ d.name }}</option>
        </select>
      </div>
      <div>
        <label class="block text-sm">Fecha y hora de inicio</label>
        <input v-model="form.start_at" type="datetime-local" class="mt-1 w-full border rounded p-2" required />
      </div>
      <div>
        <label class="block text-sm">Paciente</label>
        <input v-model="form.patient_name" class="mt-1 w-full border rounded p-2" required />
      </div>
      <div>
        <label class="block text-sm">Correo del paciente</label>
        <input v-model="form.patient_email" type="email" class="mt-1 w-full border rounded p-2" required />
      </div>
      <div>
        <label class="block text-sm">Cédula / Documento de Identidad *</label>
        <input v-model="form.cedula_paciente" class="mt-1 w-full border rounded p-2" placeholder="Ej: 1234567890" required />
      </div>
      <div>
        <label class="block text-sm">Teléfono (opcional)</label>
        <input v-model="form.telefono_paciente" type="tel" class="mt-1 w-full border rounded p-2" placeholder="Ej: 0987654321" />
      </div>
      <div>
        <label class="block text-sm">Notas</label>
        <textarea v-model="form.notes" class="mt-1 w-full border rounded p-2" />
      </div>
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Guardar</button>
    </form>
  </div>
</template>

<script setup>
import { router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import BackLink from '../../Components/BackLink.vue'
import HomeButton from '../../Components/HomeButton.vue'
const props = defineProps({ doctors: Array })
const page = usePage()
const hasErrors = computed(() => Object.keys(page.props.errors || {}).length > 0)
const form = ref({ 
  doctor_id: props.doctors?.[0]?.id || '', 
  start_at: '', 
  patient_name: '', 
  patient_email: '', 
  cedula_paciente: '',
  telefono_paciente: '',
  notes: '' 
})
function submit() { router.post(route('panel.appointments.store'), form.value) }
</script>
