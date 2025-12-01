<template>
  <AppLayout title="Diagnósticos - Administración">
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-start">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
              <i class="fas fa-file-medical text-blue-600"></i>
              Diagnósticos del Sistema
            </h1>
            <p class="text-gray-600 mt-2">
              Gestión completa de todos los diagnósticos médicos registrados
            </p>
          </div>
          <Link 
            :href="route('admin.diagnostics.patient-history')"
            class="px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl">
            <i class="fas fa-notes-medical mr-2"></i>Historia Clínica por Paciente
          </Link>
        </div>

        <!-- Filtros de Búsqueda -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
          <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Cédula específica
              </label>
              <input 
                v-model="filters.cedula"
                type="text"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500"
                placeholder="Cédula..."/>
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

        <!-- Estadísticas rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
            <p class="text-sm opacity-90">Total Diagnósticos</p>
            <p class="text-3xl font-bold mt-1">{{ diagnostics.total || 0 }}</p>
          </div>
          <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white">
            <p class="text-sm opacity-90">Pacientes Atendidos</p>
            <p class="text-3xl font-bold mt-1">{{ diagnostics.total || 0 }}</p>
          </div>
          <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white">
            <p class="text-sm opacity-90">Diagnósticos hoy</p>
            <p class="text-3xl font-bold mt-1">0</p>
          </div>
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
              <Link :href="`/admin/diagnostics/${diagnostic.id}`"
                 class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                <i class="fas fa-eye"></i>
                Ver
              </Link>
            </div>
            <div class="border-t border-gray-200 pt-4">
              <p class="text-gray-700 whitespace-pre-wrap line-clamp-3">{{ diagnostic.diagnostico }}</p>
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
  cedula: props.filters?.cedula || '',
})

function applyFilters() {
  router.get('/admin/diagnostics', filters.value, {
    preserveState: true,
    preserveScroll: true
  })
}

function clearFilters() {
  filters.value = { search: '', cedula: '' }
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
