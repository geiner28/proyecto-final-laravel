<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-gray-900 mb-3 flex items-center justify-center gap-3">
          <i class="fas fa-file-medical text-blue-600"></i>
          Consultar Historia Clínica
        </h1>
        <p class="text-gray-600 max-w-2xl mx-auto">
          Ingrese su número de cédula para consultar todos sus diagnósticos médicos registrados en el sistema
        </p>
      </div>

      <!-- Formulario de Búsqueda -->
      <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
        <form @submit.prevent="buscarDiagnosticos" class="max-w-md mx-auto">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              <i class="fas fa-id-card text-blue-600 mr-2"></i>
              Número de Cédula / Documento de Identidad
            </label>
            <input 
              v-model="cedula"
              type="text"
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-lg px-4 py-3"
              placeholder="Ej: 1234567890"
              required
            />
          </div>
          <button 
            type="submit"
            class="w-full py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition flex items-center justify-center gap-2 shadow-md">
            <i class="fas fa-search"></i>
            Buscar Diagnósticos
          </button>
        </form>
        
        <div class="mt-6 text-center">
          <Link href="/" class="text-blue-600 hover:text-blue-800 inline-flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            Volver al Inicio
          </Link>
        </div>
      </div>

      <!-- Resultados -->
      <div v-if="diagnostics && diagnostics.length > 0" class="space-y-6">
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow">
          <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 text-2xl mr-3"></i>
            <div>
              <p class="font-semibold text-green-800">Se encontraron {{ diagnostics.length }} diagnóstico(s)</p>
              <p class="text-sm text-green-700">Cédula consultada: {{ cedula }}</p>
            </div>
          </div>
        </div>

        <!-- Lista de Diagnósticos -->
        <div v-for="diagnostic in diagnostics" :key="diagnostic.id" 
             class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
          <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 text-white">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-xl font-bold flex items-center gap-2">
                  <i class="fas fa-file-medical-alt"></i>
                  Diagnóstico #{{ diagnostic.id }}
                </h3>
                <p class="text-sm text-blue-100 mt-1">
                  {{ formatDate(diagnostic.fecha_consulta) }}
                </p>
              </div>
              <span class="px-4 py-2 bg-white/20 rounded-lg backdrop-blur text-sm font-semibold">
                {{ diagnostic.especialidad || 'Medicina General' }}
              </span>
            </div>
          </div>

          <div class="p-6 space-y-4">
            <!-- Información del Médico -->
            <div class="flex items-center gap-3 pb-4 border-b border-gray-200">
              <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                {{ diagnostic.doctor.name.charAt(0) }}
              </div>
              <div>
                <p class="font-semibold text-gray-900">Dr(a). {{ diagnostic.doctor.name }}</p>
                <p class="text-sm text-gray-600">{{ diagnostic.especialidad || 'Médico General' }}</p>
              </div>
            </div>

            <!-- Diagnóstico -->
            <div>
              <h4 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                <i class="fas fa-stethoscope text-blue-600"></i>
                Diagnóstico Clínico
              </h4>
              <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <p class="text-gray-800 whitespace-pre-wrap">{{ diagnostic.diagnostico }}</p>
              </div>
            </div>

            <!-- Medicamentos -->
            <div v-if="diagnostic.medicamentos">
              <h4 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                <i class="fas fa-pills text-green-600"></i>
                Medicamentos Recetados
              </h4>
              <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
                <p class="text-gray-800 whitespace-pre-wrap">{{ diagnostic.medicamentos }}</p>
              </div>
            </div>

            <!-- Procedimientos -->
            <div v-if="diagnostic.procedimientos">
              <h4 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                <i class="fas fa-procedures text-purple-600"></i>
                Procedimientos Realizados
              </h4>
              <div class="bg-purple-50 border-l-4 border-purple-500 p-4 rounded">
                <p class="text-gray-800 whitespace-pre-wrap">{{ diagnostic.procedimientos }}</p>
              </div>
            </div>

            <!-- Remisiones -->
            <div v-if="diagnostic.remisiones">
              <h4 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                <i class="fas fa-hospital text-red-600"></i>
                Remisiones
              </h4>
              <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
                <p class="text-gray-800 whitespace-pre-wrap">{{ diagnostic.remisiones }}</p>
              </div>
            </div>

            <!-- Observaciones -->
            <div v-if="diagnostic.observaciones">
              <h4 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                <i class="fas fa-clipboard text-yellow-600"></i>
                Observaciones
              </h4>
              <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                <p class="text-gray-800 whitespace-pre-wrap">{{ diagnostic.observaciones }}</p>
              </div>
            </div>

            <!-- Botón Descargar PDF -->
            <div class="pt-4 border-t border-gray-200">
              <a 
                :href="`/diagnostics/${diagnostic.id}/pdf`"
                target="_blank"
                class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition shadow-md">
                <i class="fas fa-file-pdf"></i>
                Descargar PDF
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Sin Resultados -->
      <div v-else-if="cedula && diagnostics && diagnostics.length === 0" 
           class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-lg shadow">
        <div class="flex items-start">
          <i class="fas fa-info-circle text-yellow-500 text-3xl mr-4 mt-1"></i>
          <div>
            <h3 class="font-semibold text-yellow-800 text-lg mb-2">No se encontraron diagnósticos</h3>
            <p class="text-yellow-700 mb-3">
              No hay diagnósticos registrados para la cédula <strong>{{ cedula }}</strong>
            </p>
            <p class="text-sm text-yellow-600">
              Esto puede deberse a que:
            </p>
            <ul class="text-sm text-yellow-600 list-disc list-inside ml-2 mt-1">
              <li>No ha tenido consultas médicas en nuestro sistema</li>
              <li>Sus consultas aún no han sido completadas con diagnóstico</li>
              <li>El número de cédula ingresado no coincide con el registrado</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'

const props = defineProps({
  diagnostics: Array,
  cedula: String,
})

const cedula = ref(props.cedula || '')

function buscarDiagnosticos() {
  router.post('/consultar-diagnostico', {
    cedula: cedula.value
  }, {
    preserveState: true,
    preserveScroll: true
  })
}

function formatDate(dateString) {
  const date = new Date(dateString)
  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>
