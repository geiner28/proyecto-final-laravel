<template>
  <AppLayout title="Historia Clínica de Pacientes">
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <i class="fas fa-file-medical text-blue-600"></i>
            Historia Clínica de Pacientes
          </h1>
          <p class="text-gray-600 mt-2">
            Consulte todos los diagnósticos de pacientes atendidos en el sistema
          </p>
        </div>

        <!-- Filtros de Búsqueda -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
          <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Buscar por nombre, cédula o email
              </label>
              <input 
                v-model="filters.search"
                type="text"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500"
                placeholder="Buscar..."/>
            </div>
            <div class="flex items-end gap-2">
              <button 
                type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-search mr-2"></i>Buscar
              </button>
              <button 
                type="button"
                @click="clearFilters"
                class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-times mr-2"></i>Limpiar
              </button>
            </div>
          </form>
        </div>

        <!-- Lista de Diagnósticos -->
        <div v-if="diagnostics.data && diagnostics.data.length > 0" class="space-y-4">
          <div v-for="diagnostic in diagnostics.data" :key="diagnostic.id"
               class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-start justify-between mb-4">
              <div class="flex-1">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                  {{ diagnostic.patient_name }}
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                  <div>
                    <p class="text-gray-600">Cédula</p>
                    <p class="font-semibold">{{ diagnostic.cedula_paciente }}</p>
                  </div>
                  <div>
                    <p class="text-gray-600">Fecha</p>
                    <p class="font-semibold">{{ formatDate(diagnostic.fecha_consulta) }}</p>
                  </div>
                  <div>
                    <p class="text-gray-600">Médico</p>
                    <p class="font-semibold">Dr(a). {{ diagnostic.doctor.name }}</p>
                  </div>
                  <div>
                    <p class="text-gray-600">Especialidad</p>
                    <p class="font-semibold">{{ diagnostic.especialidad || 'General' }}</p>
                  </div>
                </div>
              </div>
              <Link :href="`/doctor/diagnostics/${diagnostic.id}`"
                 class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                <i class="fas fa-eye"></i>
                Ver
              </Link>
            </div>
            <div class="border-t border-gray-200 pt-4">
              <p class="text-gray-700 whitespace-pre-wrap">{{ diagnostic.diagnostico }}</p>
            </div>
          </div>
        </div>

        <!-- Sin resultados -->
        <div v-else class="bg-white rounded-xl shadow-md p-12 text-center">
          <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
          <h3 class="text-xl font-semibold text-gray-700 mb-2">No hay diagnósticos</h3>
          <p class="text-gray-500">No se encontraron diagnósticos con los filtros aplicados</p>
        </div>

        <!-- Paginación -->
        <div v-if="diagnostics.data && diagnostics.data.length > 0" class="mt-6 flex justify-center">
          <nav class="flex items-center gap-2">
            <Link v-for="link in diagnostics.links" :key="link.label"
                  :href="link.url"
                  :class="[
                    'px-4 py-2 rounded-lg transition',
                    link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'
                  ]"
                  v-html="link.label">
            </Link>
          </nav>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  diagnostics: Object,
  filters: Object,
})

const filters = ref({
  search: props.filters?.search || '',
})

function applyFilters() {
  router.get('/doctor/diagnostics', filters.value, {
    preserveState: true,
    preserveScroll: true
  })
}

function clearFilters() {
  filters.value = { search: '' }
  applyFilters()
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
