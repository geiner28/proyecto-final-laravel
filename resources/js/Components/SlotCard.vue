<template>
  <div class="group relative rounded-xl border bg-white/70 backdrop-blur-sm p-4 shadow-sm hover:shadow-md transition-shadow">
    <div class="text-sm font-medium text-gray-700 flex items-center gap-1">
      <span class="inline-block w-2 h-2 rounded-full" :class="statusColor"></span>
      {{ startFmt }}
    </div>
    <div class="text-xs text-gray-500">Termina: {{ endFmt }}</div>
    <button
      v-if="reserve"
      @click="reserve({ start, end, doctor })"
      class="mt-3 w-full inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400"
    >
      Reservar
    </button>
  </div>
</template>

<script setup>
const props = defineProps({
  start: String,
  end: String,
  doctor: String,
  reserve: Function,
})

function fmt(iso) {
  const d = new Date(iso)
  return d.toLocaleString(undefined, { hour: '2-digit', minute: '2-digit', day: '2-digit', month: 'short' })
}
const startFmt = fmt(props.start)
const endFmt = fmt(props.end)
const statusColor = 'bg-blue-500'
</script>