<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <AdminHeader subtitle="Gestión de Médicos" />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Médicos</h1>
          <p class="text-gray-600 mt-1">Administra el equipo médico del sistema</p>
        </div>
        <a :href="route('panel.doctors.create')" class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-lg font-semibold shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
          <i class="fas fa-plus"></i>
          Crear Médico
        </a>
      </div>
      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
      <table class="min-w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Especialidad</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="d in doctors" :key="d.slug" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="font-medium text-gray-900">{{ d.name }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">{{ d.specialty }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ d.email || 'No asignado' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span v-if="d.user_id" class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                ✓ Con acceso
              </span>
              <span v-else class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">
                Sin acceso
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
              <a :href="route('panel.doctors.edit', d.slug)" class="text-blue-600 hover:text-blue-800 font-medium">Editar</a>
              <button @click="verCalendario(d.slug)" class="text-indigo-600 hover:text-indigo-800 font-medium">Calendario</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    </div>
  </div>
</template>

<script setup>
import AdminHeader from '../../Components/AdminHeader.vue'
import { router } from '@inertiajs/vue3'
const props = defineProps({ doctors: Array })
function verCalendario(slug) { router.get('/calendar', { doctor: slug }) }
</script>
