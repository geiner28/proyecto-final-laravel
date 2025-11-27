<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <AdminHeader subtitle="Detalle de Cita" />
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <BackLink fallback="/admin/appointments" />
      <h1 class="text-3xl font-bold text-gray-900 mt-4 mb-6">Detalle de cita</h1>
      <div class="bg-white rounded-xl shadow-lg p-6 space-y-4">
        <div class="border-b pb-3">
          <p class="text-sm text-gray-500">Paciente</p>
          <p class="text-lg font-semibold text-gray-900">{{ appointment.patient_name }}</p>
        </div>
        <div class="border-b pb-3">
          <p class="text-sm text-gray-500">Correo</p>
          <p class="text-lg text-gray-900">{{ appointment.patient_email }}</p>
        </div>
        <div class="border-b pb-3">
          <p class="text-sm text-gray-500">Médico</p>
          <p class="text-lg font-semibold text-gray-900">{{ appointment.doctor?.name }}</p>
        </div>
        <div class="border-b pb-3">
          <p class="text-sm text-gray-500">Fecha y hora</p>
          <p class="text-lg text-gray-900">{{ formatear(appointment.start_at) }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-500">Estado</p>
          <p class="text-lg font-semibold" :class="{
            'text-yellow-600': appointment.status === 'pendiente',
            'text-green-600': appointment.status === 'confirmada',
            'text-red-600': appointment.status === 'rechazada'
          }">{{ appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1) }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import BackLink from '../../Components/BackLink.vue'
import AdminHeader from '../../Components/AdminHeader.vue'
const props = defineProps({ appointment: Object })
function formatear(iso) { const d = new Date(iso); return d.toLocaleString() }
</script>
