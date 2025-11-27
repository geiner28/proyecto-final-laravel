<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <AdminHeader subtitle="Editar Cita" />
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <BackLink fallback="/admin/appointments" />
      <h1 class="text-3xl font-bold text-gray-900 mt-4 mb-6">Editar cita</h1>
      <form @submit.prevent="submit" class="bg-white rounded-xl shadow-lg p-6 space-y-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Paciente</label>
          <input v-model="form.patient_name" class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" required />
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Correo del paciente</label>
          <input v-model="form.patient_email" type="email" class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" required />
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Notas</label>
          <textarea v-model="form.notes" rows="4" class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" />
        </div>
        <button class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
          <i class="fas fa-save"></i>
          Actualizar Cita
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import BackLink from '../../Components/BackLink.vue'
import AdminHeader from '../../Components/AdminHeader.vue'
const props = defineProps({ appointment: Object, doctors: Array })
const form = ref({ patient_name: props.appointment.patient_name, patient_email: props.appointment.patient_email, notes: props.appointment.notes || '' })
function submit() { router.put(route('panel.appointments.update', props.appointment.slug), form.value) }
</script>
