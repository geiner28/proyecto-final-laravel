<template>
  <div class="p-6">
    <HomeButton />
    <BackLink fallback="/admin/doctors" />
    <h1 class="text-2xl font-bold">{{ doctor.name }}</h1>
    <p>Especialidad: {{ doctor.specialty }}</p>
    <p>Correo: {{ doctor.email || 'N/A' }}</p>
    <button @click="verCalendario" class="mt-4 px-4 py-2 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700">Ver calendario</button>
  </div>
</template>

<script setup>
const props = defineProps({ doctor: Object })
import BackLink from '../../Components/BackLink.vue'
import HomeButton from '../../Components/HomeButton.vue'

import { router } from '@inertiajs/vue3'
function verCalendario() { router.get('/calendar', { doctor: props.doctor.slug }) }
</script>
