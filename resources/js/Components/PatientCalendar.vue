<template>
  <div class="rounded-2xl border bg-white/90 p-5 shadow-sm space-y-4">
    <!-- Navegación mes -->
    <div class="flex items-center justify-between">
      <button @click="previousMonth" class="p-2 hover:bg-gray-100 rounded-lg transition">
        <i class="fas fa-chevron-left text-gray-600"></i>
      </button>
      <h3 class="text-lg font-bold text-gray-800">{{ monthYearLabel }}</h3>
      <button @click="nextMonth" class="p-2 hover:bg-gray-100 rounded-lg transition">
        <i class="fas fa-chevron-right text-gray-600"></i>
      </button>
    </div>

    <!-- Instrucción -->
    <p class="text-sm text-gray-600 text-center">Haz clic en un día para ver los horarios disponibles</p>

    <!-- Días de la semana -->
    <div class="grid grid-cols-7 gap-1 text-center text-xs font-semibold text-gray-500 mb-2">
      <div>Lun</div>
      <div>Mar</div>
      <div>Mié</div>
      <div>Jue</div>
      <div>Vie</div>
      <div>Sáb</div>
      <div>Dom</div>
    </div>

    <!-- Cuadrícula de días del mes -->
    <div class="grid grid-cols-7 gap-2">
      <div
        v-for="day in calendarDays"
        :key="day.key"
        @click="day.hasSlots ? selectDay(day.date) : null"
        :class="[
          'aspect-square flex items-center justify-center rounded-lg text-sm font-medium transition',
          day.hasSlots ? 'cursor-pointer bg-blue-50 text-blue-700 hover:bg-blue-100' : 'bg-gray-50 text-gray-400 cursor-not-allowed',
          day.isToday ? 'ring-2 ring-blue-400' : '',
          day.isOtherMonth ? 'opacity-30' : '',
          selectedDate === day.date ? 'bg-blue-600 text-white hover:bg-blue-700' : ''
        ]"
      >
        <span>{{ day.day }}</span>
      </div>
    </div>

    <!-- Lista de horarios del día seleccionado -->
    <div v-if="selectedDate" class="border-t pt-4">
      <h4 class="text-sm font-semibold text-gray-700 mb-3">
        Horarios disponibles para {{ formatDateLong(selectedDate) }}
      </h4>
      <div v-if="selectedDaySlots.length === 0" class="text-center py-4 text-gray-500 text-sm">
        No hay horarios disponibles este día
      </div>
      <div v-else class="flex flex-wrap gap-2">
        <button
          v-for="slot in selectedDaySlots"
          :key="slot.start"
          @click="selectSlot(slot.start)"
          :class="[
            'px-4 py-2 rounded-lg text-sm font-medium transition',
            selected === slot.start
              ? 'bg-blue-600 text-white'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          ]"
        >
          {{ timeLabel(slot.start) }}
        </button>
      </div>
    </div>

    <!-- Horario seleccionado -->
    <div v-if="selected" class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm">
      <span class="text-gray-600">Horario seleccionado:</span>
      <span class="font-semibold text-gray-800 ml-2">{{ formattedSelected }}</span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  availability: { type: Array, default: () => [] },
  initial: { type: String, default: null },
  duration: { type: Number, default: 20 },
})

const emits = defineEmits(['select'])

// Estado
const selected = ref(props.initial)
const currentMonth = ref(new Date())
const selectedDate = ref(null) // NO auto-seleccionar ningún día al inicio

// Inicializar MES basado en initial, pero NO seleccionar el día
watch(() => props.initial, (v) => {
  selected.value = v
  if (v) {
    const d = new Date(v)
    // Solo actualizar el mes, NO el día seleccionado
    currentMonth.value = new Date(d.getFullYear(), d.getMonth(), 1)
  }
}, { immediate: true })

// Agrupar slots por fecha
const slotsByDate = computed(() => {
  const map = {}
  for (const slot of props.availability) {
    const d = new Date(slot.start)
    const key = d.toISOString().slice(0, 10)
    if (!map[key]) map[key] = []
    map[key].push(slot)
  }
  return map
})

// Generar cuadrícula del calendario
const calendarDays = computed(() => {
  const year = currentMonth.value.getFullYear()
  const month = currentMonth.value.getMonth()
  
  // Primer día del mes
  const firstDay = new Date(year, month, 1)
  // Último día del mes
  const lastDay = new Date(year, month + 1, 0)
  
  // Ajustar para que la semana empiece en lunes (0=dom, 1=lun)
  let startDayOfWeek = firstDay.getDay()
  startDayOfWeek = startDayOfWeek === 0 ? 6 : startDayOfWeek - 1
  
  const days = []
  const today = new Date().toISOString().slice(0, 10)
  
  // Días del mes anterior (para completar primera semana)
  for (let i = startDayOfWeek - 1; i >= 0; i--) {
    const d = new Date(year, month, -i)
    const dateStr = d.toISOString().slice(0, 10)
    days.push({
      key: `prev-${i}`,
      day: d.getDate(),
      date: dateStr,
      isOtherMonth: true,
      isToday: dateStr === today,
      hasSlots: !!slotsByDate.value[dateStr]
    })
  }
  
  // Días del mes actual
  for (let day = 1; day <= lastDay.getDate(); day++) {
    const d = new Date(year, month, day)
    const dateStr = d.toISOString().slice(0, 10)
    days.push({
      key: `current-${day}`,
      day: day,
      date: dateStr,
      isOtherMonth: false,
      isToday: dateStr === today,
      hasSlots: !!slotsByDate.value[dateStr]
    })
  }
  
  // Días del mes siguiente (para completar última semana)
  const remaining = 7 - (days.length % 7)
  if (remaining < 7) {
    for (let i = 1; i <= remaining; i++) {
      const d = new Date(year, month + 1, i)
      const dateStr = d.toISOString().slice(0, 10)
      days.push({
        key: `next-${i}`,
        day: d.getDate(),
        date: dateStr,
        isOtherMonth: true,
        isToday: dateStr === today,
        hasSlots: !!slotsByDate.value[dateStr]
      })
    }
  }
  
  return days
})

// Slots del día seleccionado
const selectedDaySlots = computed(() => {
  if (!selectedDate.value) return []
  return slotsByDate.value[selectedDate.value] || []
})

// Etiqueta mes/año
const monthYearLabel = computed(() => {
  return currentMonth.value.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' })
})

const formattedSelected = computed(() => {
  if (!selected.value) return '—'
  const d = new Date(selected.value)
  return d.toLocaleString('es-ES', { 
    weekday: 'long', 
    day: 'numeric', 
    month: 'long',
    hour: '2-digit',
    minute: '2-digit'
  })
})

// Métodos
function previousMonth() {
  currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() - 1, 1)
}

function nextMonth() {
  currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() + 1, 1)
}

function selectDay(date) {
  selectedDate.value = date
}

function selectSlot(iso) {
  selected.value = iso
  emits('select', iso)
}

function timeLabel(iso) {
  const d = new Date(iso)
  return d.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })
}

function formatDateLong(dateStr) {
  const d = new Date(dateStr)
  return d.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long' })
}
</script>

<style scoped>
</style>
