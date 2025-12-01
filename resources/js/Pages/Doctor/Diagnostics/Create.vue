<template>
  <AppLayout title="Crear Diagnóstico">
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-12">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <Link :href="route('doctor.appointments.index')" 
                class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-4">
            <i class="fas fa-arrow-left mr-2"></i>
            Volver al Dashboard
          </Link>
          <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <i class="fas fa-file-medical text-blue-600"></i>
            Crear Diagnóstico Médico
          </h1>
          <p class="text-gray-600 mt-2">Complete el diagnóstico para finalizar la consulta</p>
        </div>

        <!-- Información de la Cita -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-info-circle text-blue-600"></i>
            Información de la Consulta
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-gray-600">Paciente</p>
              <p class="font-semibold text-gray-900">{{ appointment.patient_name }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Cédula</p>
              <p class="font-semibold text-gray-900">{{ appointment.cedula_paciente }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Email</p>
              <p class="font-semibold text-gray-900">{{ appointment.patient_email }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Fecha de Consulta</p>
              <p class="font-semibold text-gray-900">{{ formatDate(appointment.start_at) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Médico</p>
              <p class="font-semibold text-gray-900">Dr(a). {{ appointment.doctor.name }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Especialidad</p>
              <p class="font-semibold text-gray-900">{{ appointment.doctor.specialty }}</p>
            </div>
          </div>
        </div>

        <!-- Formulario de Diagnóstico -->
        <form @submit.prevent="submitDiagnostic" class="bg-white rounded-xl shadow-md p-6 space-y-6">
          <!-- Diagnóstico Principal -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              <i class="fas fa-stethoscope text-blue-600 mr-2"></i>
              Diagnóstico Clínico <span class="text-red-500">*</span>
            </label>
            <textarea 
              v-model="form.diagnostico"
              rows="5"
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
              placeholder="Describa el diagnóstico principal de la consulta..."
              required
            ></textarea>
            <p v-if="errors.diagnostico" class="mt-1 text-sm text-red-600">{{ errors.diagnostico }}</p>
          </div>

          <!-- Medicamentos -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              <i class="fas fa-pills text-green-600 mr-2"></i>
              Medicamentos Recetados
            </label>
            <textarea 
              v-model="form.medicamentos"
              rows="4"
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
              placeholder="Ejemplo:
- Paracetamol 500mg - 1 tableta cada 8 horas por 5 días
- Ibuprofeno 400mg - 1 tableta cada 12 horas por 3 días"
            ></textarea>
            <p v-if="errors.medicamentos" class="mt-1 text-sm text-red-600">{{ errors.medicamentos }}</p>
          </div>

          <!-- Procedimientos -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              <i class="fas fa-procedures text-purple-600 mr-2"></i>
              Procedimientos Realizados
            </label>
            <textarea 
              v-model="form.procedimientos"
              rows="3"
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
              placeholder="Describa los procedimientos médicos realizados durante la consulta..."
            ></textarea>
            <p v-if="errors.procedimientos" class="mt-1 text-sm text-red-600">{{ errors.procedimientos }}</p>
          </div>

          <!-- Remisiones -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              <i class="fas fa-hospital text-red-600 mr-2"></i>
              Remisiones a Especialistas
            </label>
            <textarea 
              v-model="form.remisiones"
              rows="3"
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
              placeholder="Indique si se requiere remisión a algún especialista..."
            ></textarea>
            <p v-if="errors.remisiones" class="mt-1 text-sm text-red-600">{{ errors.remisiones }}</p>
          </div>

          <!-- Observaciones -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              <i class="fas fa-clipboard text-yellow-600 mr-2"></i>
              Observaciones y Recomendaciones Adicionales
            </label>
            <textarea 
              v-model="form.observaciones"
              rows="4"
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
              placeholder="Incluya recomendaciones generales, cuidados especiales, signos de alarma, etc..."
            ></textarea>
            <p v-if="errors.observaciones" class="mt-1 text-sm text-red-600">{{ errors.observaciones }}</p>
          </div>

          <!-- Alert -->
          <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <div class="flex items-start">
              <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
              <div class="text-sm text-blue-700">
                <p class="font-semibold mb-1">Información Importante</p>
                <p>Al guardar este diagnóstico:</p>
                <ul class="list-disc list-inside ml-2 mt-1">
                  <li>La cita será marcada automáticamente como completada</li>
                  <li>El paciente recibirá una copia del diagnóstico por correo electrónico</li>
                  <li>El diagnóstico quedará registrado en la historia clínica del paciente</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Botones -->
          <div class="flex justify-end gap-4 pt-4">
            <Link :href="route('doctor.appointments.index')" 
                  class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition">
              Cancelar
            </Link>
            <button 
              type="submit" 
              :disabled="processing"
              class="px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition disabled:opacity-50 flex items-center gap-2">
              <i class="fas fa-save"></i>
              <span v-if="!processing">Guardar Diagnóstico y Completar Cita</span>
              <span v-else>Guardando...</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  appointment: Object,
  errors: Object,
})

const form = ref({
  diagnostico: '',
  medicamentos: '',
  procedimientos: '',
  remisiones: '',
  observaciones: '',
})

const processing = ref(false)

function formatDate(dateString) {
  const date = new Date(dateString)
  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function submitDiagnostic() {
  processing.value = true
  
  router.post(
    route('doctor.diagnostics.store', props.appointment.slug),
    form.value,
    {
      onFinish: () => {
        processing.value = false
      },
      onSuccess: () => {
        // Redirigir al dashboard con mensaje de éxito
      }
    }
  )
}
</script>
