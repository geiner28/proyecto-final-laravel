<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 relative overflow-x-hidden">
    <!-- Animated blobs background -->
    <div class="absolute top-20 -left-20 w-48 sm:w-72 h-48 sm:h-72 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob pointer-events-none"></div>
    <div class="absolute top-40 -right-20 w-48 sm:w-72 h-48 sm:h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob animation-delay-2000 pointer-events-none"></div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 relative z-10">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 sm:mb-8 fade-in-up">
        <div class="space-y-2">
          <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">{{ doctor.name }}</h1>
          <div class="flex flex-wrap gap-2 items-center text-xs sm:text-sm">
            <span class="px-3 py-1 rounded-full bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 font-semibold">
              <i class="fas fa-stethoscope mr-1"></i>{{ doctor.specialty }}
            </span>
            <span class="text-gray-600">{{ doctor.email || 'N/A' }}</span>
          </div>
        </div>
        <div class="flex gap-2">
          <HomeButton />
          <BackLink fallback="/explorar" />
        </div>
      </div>

      <!-- Progress Stepper -->
      <div class="glass rounded-2xl p-4 sm:p-6 mb-6 sm:mb-8 fade-in-up animation-delay-200">
        <div class="flex items-center justify-between relative">
          <!-- Step 1 -->
          <div class="flex flex-col items-center flex-1 relative z-10">
            <button 
              @click="goToStep(1)"
              :class="[
                'w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center font-bold text-sm sm:text-base transition-all duration-300 transform',
                currentStep >= 1 
                  ? 'bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg scale-110' 
                  : 'bg-gray-200 text-gray-400',
                currentStep === 1 && 'ring-4 ring-blue-200 animate-pulse'
              ]"
            >
              <i v-if="currentStep > 1" class="fas fa-check"></i>
              <span v-else>1</span>
            </button>
            <span :class="['mt-2 text-xs sm:text-sm font-medium text-center', currentStep >= 1 ? 'text-gray-900' : 'text-gray-400']">
              Seleccionar<br class="sm:hidden" /> horario
            </span>
          </div>

          <!-- Connector Line 1-2 -->
          <div class="flex-1 h-1 mx-2 relative" style="top: -20px;">
            <div class="absolute inset-0 bg-gray-200 rounded"></div>
            <div 
              :class="['absolute inset-0 bg-gradient-to-r from-blue-500 to-indigo-600 rounded transition-all duration-500 transform origin-left', currentStep >= 2 ? 'scale-x-100' : 'scale-x-0']"
            ></div>
          </div>

          <!-- Step 2 -->
          <div class="flex flex-col items-center flex-1 relative z-10">
            <button 
              @click="goToStep(2)"
              :disabled="!selectedSlot"
              :class="[
                'w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center font-bold text-sm sm:text-base transition-all duration-300 transform',
                currentStep >= 2 
                  ? 'bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg scale-110' 
                  : 'bg-gray-200 text-gray-400',
                currentStep === 2 && 'ring-4 ring-blue-200 animate-pulse',
                !selectedSlot && currentStep < 2 && 'cursor-not-allowed'
              ]"
            >
              <i v-if="currentStep > 2" class="fas fa-check"></i>
              <span v-else>2</span>
            </button>
            <span :class="['mt-2 text-xs sm:text-sm font-medium text-center', currentStep >= 2 ? 'text-gray-900' : 'text-gray-400']">
              Datos del<br class="sm:hidden" /> paciente
            </span>
          </div>

          <!-- Connector Line 2-3 -->
          <div class="flex-1 h-1 mx-2 relative" style="top: -20px;">
            <div class="absolute inset-0 bg-gray-200 rounded"></div>
            <div 
              :class="['absolute inset-0 bg-gradient-to-r from-blue-500 to-indigo-600 rounded transition-all duration-500 transform origin-left', currentStep >= 3 ? 'scale-x-100' : 'scale-x-0']"
            ></div>
          </div>

          <!-- Step 3 -->
          <div class="flex flex-col items-center flex-1 relative z-10">
            <button 
              :class="[
                'w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center font-bold text-sm sm:text-base transition-all duration-300 transform',
                currentStep >= 3 
                  ? 'bg-gradient-to-br from-green-500 to-emerald-600 text-white shadow-lg scale-110' 
                  : 'bg-gray-200 text-gray-400',
                currentStep === 3 && 'ring-4 ring-green-200'
              ]"
            >
              <i v-if="currentStep >= 3" class="fas fa-check-circle"></i>
              <span v-else>3</span>
            </button>
            <span :class="['mt-2 text-xs sm:text-sm font-medium text-center', currentStep >= 3 ? 'text-gray-900' : 'text-gray-400']">
              Confirmación
            </span>
          </div>
        </div>
      </div>

      <!-- Step Content with Transitions -->
      <div class="relative min-h-[500px]">
        <!-- Step 1: Calendar Selection -->
        <Transition name="slide-fade" mode="out-in">
          <div v-if="currentStep === 1" key="step1" class="fade-in-up animation-delay-400">
            <div class="glass rounded-2xl p-4 sm:p-6 lg:p-8 shadow-xl">
              <div class="mb-4 sm:mb-6">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">
                  <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                  Selecciona fecha y horario
                </h2>
                <p class="text-sm sm:text-base text-gray-600">Elige el día y horario que mejor se ajuste a tu disponibilidad</p>
              </div>
              
              <PatientCalendar 
                :availability="availability" 
                :initial="selectedSlot" 
                :duration="duration" 
                @select="onSelectSlot" 
              />

              <div v-if="selectedSlot" class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200 animate-fade-in">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-semibold text-gray-600">Horario seleccionado:</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">{{ formatDateTime(selectedSlot) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Duración: {{ duration }} minutos</p>
                  </div>
                  <button 
                    @click="currentStep = 2" 
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-105 transition-all duration-200"
                  >
                    Continuar
                    <i class="fas fa-arrow-right ml-2"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </Transition>

        <!-- Step 2: Patient Form -->
        <Transition name="slide-fade" mode="out-in">
          <div v-if="currentStep === 2" key="step2" class="fade-in-up animation-delay-400">
            <div class="glass rounded-2xl p-4 sm:p-6 lg:p-8 shadow-xl max-w-3xl mx-auto">
              <div class="mb-6">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">
                  <i class="fas fa-user-edit text-indigo-600 mr-2"></i>
                  Datos del paciente
                </h2>
                <p class="text-sm sm:text-base text-gray-600">Completa tu información para confirmar la cita</p>
              </div>

              <!-- Selected appointment summary -->
              <div class="mb-6 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border border-indigo-200">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                  <div>
                    <p class="text-gray-600 font-medium">Profesional</p>
                    <p class="text-gray-900 font-bold">{{ doctor.name }}</p>
                  </div>
                  <div>
                    <p class="text-gray-600 font-medium">Horario</p>
                    <p class="text-gray-900 font-bold">{{ formatDateTime(selectedSlot) }}</p>
                  </div>
                </div>
              </div>

              <form @submit.prevent="submitForm" class="space-y-4 sm:space-y-5">
                <!-- Error messages -->
                <div v-if="hasErrors" class="bg-red-50 border-2 border-red-200 rounded-xl p-4 space-y-2 animate-shake">
                  <div class="flex items-center gap-2 text-red-800 font-semibold">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Por favor corrige los siguientes errores:</span>
                  </div>
                  <p v-if="$page.props.errors.start" class="text-sm text-red-600 flex items-start gap-2 ml-6">
                    <i class="fas fa-circle text-xs mt-1"></i>
                    {{ $page.props.errors.start }}
                  </p>
                  <p v-if="$page.props.errors.cedula_paciente" class="text-sm text-red-600 flex items-start gap-2 ml-6">
                    <i class="fas fa-circle text-xs mt-1"></i>
                    {{ $page.props.errors.cedula_paciente }}
                  </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                  <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                      <i class="fas fa-user text-blue-600 mr-1"></i>
                      Nombre completo *
                    </label>
                    <input 
                      v-model="form.patient_name" 
                      class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                      placeholder="Ej: Juan Pérez"
                      required 
                    />
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                      <i class="fas fa-envelope text-indigo-600 mr-1"></i>
                      Correo electrónico *
                    </label>
                    <input 
                      v-model="form.patient_email" 
                      type="email" 
                      class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" 
                      placeholder="ejemplo@correo.com"
                      required 
                    />
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                      <i class="fas fa-id-card text-purple-600 mr-1"></i>
                      Cédula / Documento *
                    </label>
                    <input 
                      v-model="form.cedula_paciente" 
                      class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all" 
                      placeholder="1234567890" 
                      required 
                    />
                    <p class="text-xs text-gray-500">Podrás consultar tu cita con este número</p>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                      <i class="fas fa-phone text-green-600 mr-1"></i>
                      Teléfono (opcional)
                    </label>
                    <input 
                      v-model="form.telefono_paciente" 
                      type="tel" 
                      class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" 
                      placeholder="0987654321" 
                    />
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-gray-700">
                    <i class="fas fa-notes-medical text-teal-600 mr-1"></i>
                    Motivo de consulta (opcional)
                  </label>
                  <textarea 
                    v-model="form.notes" 
                    rows="4" 
                    class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all" 
                    placeholder="Describe brevemente el motivo de tu consulta, síntomas, etc."
                  ></textarea>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                  <button 
                    type="button"
                    @click="currentStep = 1" 
                    class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200 flex items-center justify-center gap-2"
                  >
                    <i class="fas fa-arrow-left"></i>
                    Volver
                  </button>
                  <button 
                    type="submit"
                    :disabled="submitting"
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-105 transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <i v-if="submitting" class="fas fa-spinner fa-spin"></i>
                    <i v-else class="fas fa-check-circle"></i>
                    {{ submitting ? 'Procesando...' : 'Confirmar reserva' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </Transition>

        <!-- Step 3: Success Confirmation -->
        <Transition name="slide-fade" mode="out-in">
          <div v-if="currentStep === 3" key="step3" class="fade-in-up animation-delay-400">
            <div class="glass rounded-2xl p-6 sm:p-8 lg:p-12 shadow-xl max-w-3xl mx-auto text-center">
              <!-- Success Animation -->
              <div class="mb-6 animate-bounce-in">
                <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-4 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center shadow-2xl animate-scale-in">
                  <i class="fas fa-check text-4xl sm:text-5xl text-white"></i>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-2">
                  ¡Cita reservada con éxito!
                </h2>
                <p class="text-base sm:text-lg text-gray-600">Tu solicitud ha sido enviada correctamente</p>
              </div>

              <!-- Appointment Details Card -->
              <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 sm:p-8 border-2 border-blue-200 mb-6 text-left animate-fade-in animation-delay-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                  <i class="fas fa-calendar-check text-blue-600"></i>
                  Detalles de tu cita
                </h3>
                
                <div class="space-y-4">
                  <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                      <i class="fas fa-user-md text-blue-600"></i>
                    </div>
                    <div>
                      <p class="text-sm text-gray-600 font-medium">Profesional</p>
                      <p class="text-base font-bold text-gray-900">{{ doctor.name }}</p>
                      <p class="text-sm text-blue-600">{{ doctor.specialty }}</p>
                    </div>
                  </div>

                  <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                      <i class="fas fa-clock text-indigo-600"></i>
                    </div>
                    <div>
                      <p class="text-sm text-gray-600 font-medium">Fecha y hora</p>
                      <p class="text-base font-bold text-gray-900">{{ formatDateTime(selectedSlot) }}</p>
                      <p class="text-sm text-gray-500">Duración: {{ duration }} minutos</p>
                    </div>
                  </div>

                  <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                      <i class="fas fa-user text-purple-600"></i>
                    </div>
                    <div>
                      <p class="text-sm text-gray-600 font-medium">Paciente</p>
                      <p class="text-base font-bold text-gray-900">{{ form.patient_name }}</p>
                      <p class="text-sm text-gray-500">{{ form.patient_email }}</p>
                    </div>
                  </div>

                  <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                      <i class="fas fa-id-card text-green-600"></i>
                    </div>
                    <div>
                      <p class="text-sm text-gray-600 font-medium">Cédula</p>
                      <p class="text-base font-bold text-gray-900">{{ form.cedula_paciente }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Info Alert -->
              <div class="bg-amber-50 border-2 border-amber-200 rounded-xl p-4 mb-6 text-left animate-fade-in animation-delay-400">
                <div class="flex gap-3">
                  <i class="fas fa-info-circle text-amber-600 text-xl flex-shrink-0 mt-0.5"></i>
                  <div class="text-sm text-amber-800">
                    <p class="font-semibold mb-1">Información importante:</p>
                    <ul class="space-y-1 list-disc list-inside">
                      <li>Recibirás un correo de confirmación en <strong>{{ form.patient_email }}</strong></li>
                      <li>Tu cita está en estado <strong>PENDIENTE</strong> hasta que el médico la confirme</li>
                      <li>Puedes consultar el estado con tu cédula en cualquier momento</li>
                    </ul>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex flex-col sm:flex-row gap-3">
                <button 
                  @click="consultarCita"
                  class="flex-1 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-105 transition-all duration-200 flex items-center justify-center gap-2"
                >
                  <i class="fas fa-search"></i>
                  Consultar mi cita
                </button>
                <button 
                  @click="volverInicio"
                  class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-105 transition-all duration-200 flex items-center justify-center gap-2"
                >
                  <i class="fas fa-home"></i>
                  Volver al inicio
                </button>
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </div>
  </div>
</template>

<script setup>
// Componente público con stepper para reservar citas
import { router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import BackLink from '../../Components/BackLink.vue'
import HomeButton from '../../Components/HomeButton.vue'
import PatientCalendar from '../../Components/PatientCalendar.vue'

const props = defineProps({
  doctor: Object,
  weekStart: String,
  duration: Number,
  availability: Array,
  success: String, // Flash message from backend
})

const page = usePage()
const currentStep = ref(1)
const selectedSlot = ref(null)
const submitting = ref(false)
const appointmentCreated = ref(false)

const form = ref({
  patient_name: '',
  patient_email: '',
  cedula_paciente: '',
  telefono_paciente: '',
  notes: '',
})

const hasErrors = computed(() => Object.keys(page.props.errors || {}).length > 0)

// If there's a success message, show step 3
if (props.success) {
  currentStep.value = 3
  appointmentCreated.value = true
}

function onSelectSlot(slotStart) {
  selectedSlot.value = slotStart
}

function goToStep(step) {
  if (step === 1) {
    currentStep.value = 1
  } else if (step === 2 && selectedSlot.value) {
    currentStep.value = 2
  }
}

function submitForm() {
  submitting.value = true
  router.post('/appointments', 
    { 
      doctor: props.doctor.slug, 
      start: selectedSlot.value, 
      ...form.value 
    },
    {
      onSuccess: () => {
        submitting.value = false
        currentStep.value = 3
      },
      onError: () => {
        submitting.value = false
      }
    }
  )
}

function formatDateTime(iso) {
  if (!iso) return ''
  const d = new Date(iso)
  const options = { 
    weekday: 'long', 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }
  return d.toLocaleString('es-ES', options)
}

function consultarCita() {
  router.get('/consultar-cita')
}

function volverInicio() {
  router.get('/')
}
</script>

<style scoped>
.glass {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(16px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.3);
}

@keyframes blob {
  0%, 100% { transform: translate(0, 0) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-200 { animation-delay: 0.2s; }
.animation-delay-400 { animation-delay: 0.4s; }
.animation-delay-2000 { animation-delay: 2s; }

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

.fade-in-up {
  animation: fade-in-up 0.6s ease-out forwards;
}

@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}

.animate-fade-in {
  animation: fade-in 0.5s ease-out forwards;
}

@keyframes bounce-in {
  0% { transform: scale(0); opacity: 0; }
  50% { transform: scale(1.1); }
  100% { transform: scale(1); opacity: 1; }
}

.animate-bounce-in {
  animation: bounce-in 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
}

@keyframes scale-in {
  0% { transform: scale(0) rotate(-180deg); }
  100% { transform: scale(1) rotate(0deg); }
}

.animate-scale-in {
  animation: scale-in 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-10px); }
  75% { transform: translateX(10px); }
}

.animate-shake {
  animation: shake 0.5s ease-in-out;
}

/* Transition classes for step changes */
.slide-fade-enter-active {
  transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.slide-fade-leave-active {
  transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.slide-fade-enter-from {
  transform: translateX(50px);
  opacity: 0;
}

.slide-fade-leave-to {
  transform: translateX(-50px);
  opacity: 0;
}
</style>
