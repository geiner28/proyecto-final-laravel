<template>
  <button @click="goHome" type="button" class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-50 text-blue-700 rounded hover:bg-blue-100">
    Inicio
  </button>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
function goHome() { router.get('/') }
</script>