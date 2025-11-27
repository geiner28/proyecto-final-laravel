<template>
  <button type="button" @click="goBack" class="inline-flex items-center text-sm text-blue-600 hover:underline">
    ← Volver
  </button>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
const props = defineProps({ fallback: { type: String, default: '/' } })
function goBack() {
  if (window.history.length > 1) {
    window.history.back()
  } else {
    router.get(props.fallback)
  }
}
</script>