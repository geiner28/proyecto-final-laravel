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
          <p class="text-sm text-gray-500">Cédula</p>
          <p class="text-lg text-gray-900">{{ appointment.cedula_paciente }}</p>
        </div>
        
        <div class="border-b pb-3">
          <p class="text-sm text-gray-500">Correo</p>
          <p class="text-lg text-gray-900">{{ appointment.patient_email }}</p>
        </div>
        
        <div v-if="appointment.telefono_paciente" class="border-b pb-3">
          <p class="text-sm text-gray-500">Teléfono</p>
          <p class="text-lg text-gray-900">{{ appointment.telefono_paciente }}</p>
        </div>
        
        <div class="border-b pb-3">
          <p class="text-sm text-gray-500">Médico</p>
          <p class="text-lg font-semibold text-gray-900">{{ appointment.doctor?.name }}</p>
          <p class="text-sm text-gray-500">{{ appointment.doctor?.specialty }}</p>
        </div>
        
        <div class="border-b pb-3">
          <p class="text-sm text-gray-500">Fecha y hora</p>
          <p class="text-lg text-gray-900">{{ formatear(appointment.start_at) }}</p>
        </div>
        
        <div class="border-b pb-3">
          <p class="text-sm text-gray-500">Estado</p>
          <p class="text-lg font-semibold" :class="{
            'text-yellow-600': appointment.status === 'pendiente',
            'text-green-600': appointment.status === 'confirmada',
            'text-blue-600': appointment.status === 'completada',
            'text-red-600': appointment.status === 'rechazada',
            'text-gray-600': appointment.status === 'cancelada'
          }">{{ appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1) }}</p>
        </div>
        
        <div v-if="appointment.notes">
          <p class="text-sm text-gray-500">Notas</p>
          <p class="text-base text-gray-900">{{ appointment.notes }}</p>
        </div>
        
        <!-- Acciones solo para Admin -->
        <div v-if="$page.props.auth.user.role === 'admin'" class="pt-6 border-t flex flex-wrap gap-3">
          <!-- Aceptar -->
          <form v-if="appointment.status === 'pendiente'" @submit.prevent="acceptAppointment" method="POST">
            <button 
              type="submit"
              class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-medium"
            >
              <i class="fas fa-check-circle mr-2"></i>Aceptar Cita
            </button>
          </form>
          
          <!-- Rechazar -->
          <form v-if="appointment.status === 'pendiente'" @submit.prevent="rejectAppointment" method="POST">
            <button 
              type="submit"
              class="px-6 py-3 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-medium"
            >
              <i class="fas fa-times-circle mr-2"></i>Rechazar Cita
            </button>
          </form>
          
          <!-- Reagendar -->
          <a 
            v-if="['pendiente', 'confirmada'].includes(appointment.status)"
            :href="route('panel.appointments.reschedule-form', appointment.slug)"
            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-medium inline-block"
          >
            <i class="fas fa-calendar-alt mr-2"></i>Reagendar
          </a>
          
          <!-- Cancelar -->
          <form v-if="['pendiente', 'confirmada'].includes(appointment.status)" @submit.prevent="cancelAppointment" method="POST">
            <button 
              type="submit"
              class="px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-700 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-medium"
            >
              <i class="fas fa-ban mr-2"></i>Cancelar Cita
            </button>
          </form>
        </div>
        
        <!-- Mensaje para médicos -->
        <div v-else-if="$page.props.auth.user.role === 'doctor'" class="pt-6 border-t bg-blue-50 p-4 rounded-lg">
          <p class="text-sm text-blue-800">
            <i class="fas fa-info-circle mr-2"></i>
            Solo el administrador puede reagendar o cancelar citas.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import BackLink from '../../Components/BackLink.vue';
import AdminHeader from '../../Components/AdminHeader.vue';

const props = defineProps({ 
  appointment: Object 
});

function formatear(iso) { 
  const d = new Date(iso); 
  return d.toLocaleString('es-ES', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}

function acceptAppointment() {
  if (confirm('¿Confirmar esta cita?')) {
    router.post(route('panel.appointments.accept', props.appointment.slug));
  }
}

function rejectAppointment() {
  if (confirm('¿Rechazar esta cita?')) {
    router.post(route('panel.appointments.reject', props.appointment.slug));
  }
}

function cancelAppointment() {
  if (confirm('¿Cancelar esta cita? Esta acción no se puede deshacer.')) {
    router.post(route('panel.appointments.cancel', props.appointment.slug));
  }
}
</script>
