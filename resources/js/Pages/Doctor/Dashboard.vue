<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    appointments: Array,
    doctor: Object,
    weekData: Array,
    weekStart: String,
    weekEnd: String,
    stats: Object,
    filters: Object
});

// Estados
const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const selectedDate = ref(props.filters.date || '');
const completeModal = ref({ show: false, appointment: null, loading: false, notes: '' });

// Formateo de fechas
const formatDate = (datetime) => {
    const date = new Date(datetime);
    return date.toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatTime = (datetime) => {
    const date = new Date(datetime);
    return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
};

const formatShortDate = (dateStr) => {
    const date = new Date(dateStr);
    return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });
};

// Helpers de estado
const getStatusText = (status) => {
    const map = {
        'pendiente': 'Pendiente',
        'confirmada': 'Confirmada',
        'rechazada': 'Rechazada',
        'completada': 'Completada',
        'cancelada': 'Cancelada'
    };
    return map[status] || status;
};

const getStatusClass = (status) => {
    const map = {
        'pendiente': 'bg-yellow-100 text-yellow-800 border-yellow-300',
        'confirmada': 'bg-green-100 text-green-800 border-green-300',
        'rechazada': 'bg-red-100 text-red-800 border-red-300',
        'completada': 'bg-blue-100 text-blue-800 border-blue-300',
        'cancelada': 'bg-gray-100 text-gray-800 border-gray-300'
    };
    return map[status] || 'bg-gray-100 text-gray-800';
};

const getStatusIcon = (status) => {
    const map = {
        'pendiente': 'fa-clock',
        'confirmada': 'fa-check-circle',
        'rechazada': 'fa-times-circle',
        'completada': 'fa-check-double',
        'cancelada': 'fa-ban'
    };
    return map[status] || 'fa-question-circle';
};

// Validaciones
const canComplete = (appointment) => {
    if (appointment.status !== 'confirmada') return false;
    const aptDateStr = appointment.start_at.substring(0, 10);
    const today = new Date();
    const todayStr = today.getFullYear() + '-' + 
                     String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                     String(today.getDate()).padStart(2, '0');
    return aptDateStr <= todayStr;
};

// Navegación de semana
const goToPreviousWeek = () => {
    const prevWeek = new Date(props.weekStart);
    prevWeek.setDate(prevWeek.getDate() - 7);
    router.get(route('doctor.appointments.index'), {
        week_start: prevWeek.toISOString().split('T')[0]
    }, { preserveState: true });
};

const goToNextWeek = () => {
    const nextWeek = new Date(props.weekStart);
    nextWeek.setDate(nextWeek.getDate() + 7);
    router.get(route('doctor.appointments.index'), {
        week_start: nextWeek.toISOString().split('T')[0]
    }, { preserveState: true });
};

const goToToday = () => {
    router.get(route('doctor.appointments.index'), {}, { preserveState: true });
};

// Filtros (independientes del calendario)
const applyFilters = () => {
    router.get(route('doctor.appointments.index'), {
        search: searchQuery.value,
        status: statusFilter.value,
        date: selectedDate.value
    }, { preserveState: true, preserveScroll: true });
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = '';
    selectedDate.value = '';
    router.get(route('doctor.appointments.index'), {}, { preserveState: true });
};

// Modal completar
const openCompleteModal = (appointment) => {
    completeModal.value = {
        show: true,
        appointment: appointment,
        loading: false,
        notes: appointment.notes || ''
    };
};

const closeCompleteModal = () => {
    completeModal.value = { show: false, appointment: null, loading: false, notes: '' };
};

const confirmComplete = () => {
    completeModal.value.loading = true;
    router.post(route('doctor.appointments.complete', completeModal.value.appointment.slug), {
        notes: completeModal.value.notes
    }, {
        preserveState: true,
        onSuccess: () => closeCompleteModal(),
        onError: () => { completeModal.value.loading = false; }
    });
};

// Citas de hoy
const todayAppointments = computed(() => {
    const today = new Date().toISOString().split('T')[0];
    return props.appointments.filter(apt => apt.start_at.substring(0, 10) === today);
});
</script>

<template>
    <AppLayout title="Dashboard Médico">
        <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <!-- Blobs de fondo -->
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>
            <div class="blob blob-3"></div>

            <div class="max-w-7xl mx-auto relative z-10">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        <i class="fas fa-user-md mr-3"></i>
                        Dashboard Médico
                    </h1>
                    <p class="mt-2 text-gray-600">Dr. {{ doctor.name }} - {{ doctor.specialty }}</p>
                </div>

                <!-- Estadísticas -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                    <div class="glass p-4 rounded-2xl hover:shadow-xl transition-all">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl mx-auto mb-2 flex items-center justify-center">
                                <i class="fas fa-calendar-day text-white text-xl"></i>
                            </div>
                            <p class="text-2xl font-bold text-indigo-600">{{ stats.today }}</p>
                            <p class="text-xs text-gray-600">Hoy</p>
                        </div>
                    </div>

                    <div class="glass p-4 rounded-2xl hover:shadow-xl transition-all">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl mx-auto mb-2 flex items-center justify-center">
                                <i class="fas fa-calendar-week text-white text-xl"></i>
                            </div>
                            <p class="text-2xl font-bold text-blue-600">{{ stats.week }}</p>
                            <p class="text-xs text-gray-600">Esta Semana</p>
                        </div>
                    </div>

                    <div class="glass p-4 rounded-2xl hover:shadow-xl transition-all">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl mx-auto mb-2 flex items-center justify-center">
                                <i class="fas fa-clock text-white text-xl"></i>
                            </div>
                            <p class="text-2xl font-bold text-yellow-600">{{ stats.pending }}</p>
                            <p class="text-xs text-gray-600">Pendientes</p>
                        </div>
                    </div>

                    <div class="glass p-4 rounded-2xl hover:shadow-xl transition-all">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl mx-auto mb-2 flex items-center justify-center">
                                <i class="fas fa-check-circle text-white text-xl"></i>
                            </div>
                            <p class="text-2xl font-bold text-green-600">{{ stats.confirmed }}</p>
                            <p class="text-xs text-gray-600">Confirmadas</p>
                        </div>
                    </div>

                    <div class="glass p-4 rounded-2xl hover:shadow-xl transition-all">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-xl mx-auto mb-2 flex items-center justify-center">
                                <i class="fas fa-check-double text-white text-xl"></i>
                            </div>
                            <p class="text-2xl font-bold text-blue-600">{{ stats.completed }}</p>
                            <p class="text-xs text-gray-600">Completadas</p>
                        </div>
                    </div>

                    <div class="glass p-4 rounded-2xl hover:shadow-xl transition-all">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl mx-auto mb-2 flex items-center justify-center">
                                <i class="fas fa-calendar-check text-white text-xl"></i>
                            </div>
                            <p class="text-2xl font-bold text-purple-600">{{ stats.total }}</p>
                            <p class="text-xs text-gray-600">Total</p>
                        </div>
                    </div>
                </div>

                <!-- Filtros Avanzados -->
                <div class="glass p-6 rounded-2xl mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-filter mr-2"></i>Filtros de Búsqueda
                        </h3>
                        <button
                            v-if="searchQuery || statusFilter || selectedDate"
                            @click="clearFilters"
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all text-sm"
                        >
                            <i class="fas fa-times mr-2"></i>Limpiar
                        </button>
                    </div>

                    <div class="grid md:grid-cols-3 gap-4">
                        <!-- Búsqueda -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-search mr-1"></i>Buscar paciente
                            </label>
                            <input
                                v-model="searchQuery"
                                @keyup.enter="applyFilters"
                                type="text"
                                placeholder="Nombre, email o cédula..."
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>

                        <!-- Estado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tag mr-1"></i>Estado
                            </label>
                            <select
                                v-model="statusFilter"
                                @change="applyFilters"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">Todos los estados</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="confirmada">Confirmada</option>
                                <option value="completada">Completada</option>
                                <option value="rechazada">Rechazada</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </div>

                        <!-- Fecha -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar mr-1"></i>Fecha específica
                            </label>
                            <input
                                v-model="selectedDate"
                                @change="applyFilters"
                                type="date"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>
                    </div>

                    <div class="flex gap-2 mt-4">
                        <button
                            @click="applyFilters"
                            class="px-6 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:shadow-lg transition-all font-medium"
                        >
                            <i class="fas fa-search mr-2"></i>Buscar
                        </button>
                    </div>
                </div>

                <!-- Calendario Semanal -->
                <div class="glass p-6 rounded-2xl mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">
                            <i class="fas fa-calendar-week text-blue-600 mr-3"></i>
                            Calendario Semanal
                        </h2>
                        <div class="flex gap-2">
                            <button
                                @click="goToPreviousWeek"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-all"
                            >
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button
                                @click="goToToday"
                                class="px-4 py-2 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-lg transition-all font-medium"
                            >
                                Hoy
                            </button>
                            <button
                                @click="goToNextWeek"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-all"
                            >
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>

                    <p class="text-gray-600 mb-6">
                        {{ formatDate(weekStart) }} - {{ formatDate(weekEnd) }}
                    </p>

                    <!-- Vista Semanal -->
                    <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
                        <div
                            v-for="day in weekData"
                            :key="day.date"
                            class="rounded-xl border-2 transition-all"
                            :class="{
                                'border-blue-500 bg-blue-50': day.isToday,
                                'border-gray-200 bg-white': !day.isToday && !day.isPast,
                                'border-gray-200 bg-gray-50': day.isPast
                            }"
                        >
                            <!-- Header del día -->
                            <div 
                                class="p-3 text-center border-b-2"
                                :class="{
                                    'bg-blue-500 text-white border-blue-600': day.isToday,
                                    'bg-gray-100 border-gray-200': !day.isToday
                                }"
                            >
                                <p class="text-xs font-semibold uppercase">{{ day.dayShort }}</p>
                                <p class="text-2xl font-bold">{{ formatShortDate(day.date).split(' ')[0] }}</p>
                                <p class="text-xs">{{ formatShortDate(day.date).split(' ')[1] }}</p>
                            </div>

                            <!-- Citas del día -->
                            <div class="p-2 space-y-2 max-h-96 overflow-y-auto">
                                <div
                                    v-if="day.appointments.length === 0"
                                    class="text-center py-4 text-gray-400 text-sm"
                                >
                                    <i class="fas fa-calendar-times text-2xl mb-2 block"></i>
                                    Sin citas
                                </div>

                                <div
                                    v-for="apt in day.appointments"
                                    :key="apt.id"
                                    class="p-3 rounded-lg border-l-4 cursor-pointer hover:shadow-md transition-all text-sm"
                                    :class="{
                                        'bg-yellow-50 border-yellow-500': apt.status === 'pendiente',
                                        'bg-green-50 border-green-500': apt.status === 'confirmada',
                                        'bg-blue-50 border-blue-500': apt.status === 'completada',
                                        'bg-red-50 border-red-500': apt.status === 'rechazada',
                                        'bg-gray-50 border-gray-500': apt.status === 'cancelada'
                                    }"
                                >
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-bold text-xs">{{ formatTime(apt.start_at) }}</span>
                                        <span
                                            class="px-2 py-0.5 rounded-full text-xs"
                                            :class="getStatusClass(apt.status)"
                                        >
                                            <i class="fas" :class="getStatusIcon(apt.status)"></i>
                                        </span>
                                    </div>
                                    <p class="font-semibold text-gray-900 truncate text-xs">{{ apt.patient_name }}</p>
                                    <p class="text-gray-600 text-xs truncate">
                                        <i class="fas fa-id-card mr-1"></i>{{ apt.cedula_paciente }}
                                    </p>
                                    <div class="mt-2 flex gap-1">
                                        <button
                                            v-if="canComplete(apt)"
                                            @click="openCompleteModal(apt)"
                                            class="flex-1 px-2 py-1 bg-blue-500 text-white rounded text-xs hover:bg-blue-600 transition-all"
                                        >
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resultados de Búsqueda Filtrados -->
                <div v-if="searchQuery || statusFilter || selectedDate" class="glass p-6 rounded-2xl mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-search text-blue-600 mr-3"></i>
                            Resultados de Búsqueda
                        </h2>
                        <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full font-semibold">
                            {{ appointments.length }} encontrada(s)
                        </span>
                    </div>

                    <div v-if="appointments.length === 0" class="text-center py-12">
                        <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
                        <p class="text-gray-600 text-lg">No se encontraron citas con los filtros aplicados</p>
                        <button
                            @click="clearFilters"
                            class="mt-4 px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all"
                        >
                            Limpiar filtros
                        </button>
                    </div>

                    <div v-else class="space-y-4">
                        <div
                            v-for="apt in appointments"
                            :key="apt.id"
                            class="bg-white p-5 rounded-xl border-l-4 hover:shadow-lg transition-all"
                            :class="{
                                'border-yellow-500': apt.status === 'pendiente',
                                'border-green-500': apt.status === 'confirmada',
                                'border-blue-500': apt.status === 'completada',
                                'border-red-500': apt.status === 'rechazada',
                                'border-gray-500': apt.status === 'cancelada'
                            }"
                        >
                            <div class="flex flex-col md:flex-row gap-4 items-start">
                                <div class="flex-shrink-0">
                                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white px-6 py-3 rounded-xl text-center">
                                        <i class="fas fa-calendar block text-sm mb-1"></i>
                                        <p class="text-lg font-bold">{{ formatShortDate(apt.start_at) }}</p>
                                        <p class="text-sm">{{ formatTime(apt.start_at) }}</p>
                                    </div>
                                </div>

                                <div class="flex-1 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-xl font-bold text-gray-900">
                                            <i class="fas fa-user-circle text-blue-600 mr-2"></i>
                                            {{ apt.patient_name }}
                                        </h3>
                                        <span :class="getStatusClass(apt.status)" class="px-3 py-1 rounded-full text-sm font-medium border">
                                            <i class="fas mr-1" :class="getStatusIcon(apt.status)"></i>
                                            {{ getStatusText(apt.status) }}
                                        </span>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-2 text-sm text-gray-600">
                                        <p><i class="fas fa-id-card mr-2 text-indigo-600"></i><strong>Cédula:</strong> {{ apt.cedula_paciente }}</p>
                                        <p><i class="fas fa-envelope mr-2 text-blue-600"></i><strong>Email:</strong> {{ apt.patient_email }}</p>
                                        <p v-if="apt.telefono_paciente"><i class="fas fa-phone mr-2 text-green-600"></i><strong>Teléfono:</strong> {{ apt.telefono_paciente }}</p>
                                        <p><i class="fas fa-calendar mr-2 text-purple-600"></i><strong>Fecha:</strong> {{ formatDate(apt.start_at) }}</p>
                                    </div>

                                    <div v-if="apt.notes" class="bg-purple-50 p-3 rounded-lg text-sm">
                                        <i class="fas fa-sticky-note text-purple-600 mr-2"></i><strong>Notas:</strong> {{ apt.notes }}
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <Link
                                        v-if="apt.status === 'completada' && apt.diagnostic"
                                        :href="route('doctor.diagnostics.show', apt.diagnostic.id)"
                                        class="px-4 py-2 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-lg hover:shadow-lg transition-all font-medium whitespace-nowrap"
                                    >
                                        <i class="fas fa-file-medical mr-2"></i>Ver Diagnóstico
                                    </Link>

                                    <button
                                        v-if="canComplete(apt)"
                                        @click="openCompleteModal(apt)"
                                        class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:shadow-lg transition-all font-medium whitespace-nowrap"
                                    >
                                        <i class="fas fa-check-double mr-2"></i>Completar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista Detallada de Citas de Hoy -->
                <div v-else-if="todayAppointments.length > 0" class="glass p-6 rounded-2xl mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-star text-yellow-500 mr-3 animate-pulse"></i>
                        Citas de Hoy
                    </h2>

                    <div class="space-y-4">
                        <div
                            v-for="apt in todayAppointments"
                            :key="apt.id"
                            class="bg-white p-5 rounded-xl border-l-4 hover:shadow-lg transition-all"
                            :class="{
                                'border-yellow-500': apt.status === 'pendiente',
                                'border-green-500': apt.status === 'confirmada',
                                'border-blue-500': apt.status === 'completada',
                                'border-red-500': apt.status === 'rechazada'
                            }"
                        >
                            <div class="flex flex-col md:flex-row gap-4 items-start">
                                <div class="flex-shrink-0">
                                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white px-6 py-3 rounded-xl text-center">
                                        <i class="fas fa-clock block text-xl mb-1"></i>
                                        <p class="text-2xl font-bold">{{ formatTime(apt.start_at) }}</p>
                                    </div>
                                </div>

                                <div class="flex-1 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-xl font-bold text-gray-900">
                                            <i class="fas fa-user-circle text-blue-600 mr-2"></i>
                                            {{ apt.patient_name }}
                                        </h3>
                                        <span :class="getStatusClass(apt.status)" class="px-3 py-1 rounded-full text-sm font-medium border">
                                            <i class="fas mr-1" :class="getStatusIcon(apt.status)"></i>
                                            {{ getStatusText(apt.status) }}
                                        </span>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-2 text-sm text-gray-600">
                                        <p><i class="fas fa-id-card mr-2"></i>{{ apt.cedula_paciente }}</p>
                                        <p><i class="fas fa-envelope mr-2"></i>{{ apt.patient_email }}</p>
                                        <p v-if="apt.telefono_paciente"><i class="fas fa-phone mr-2"></i>{{ apt.telefono_paciente }}</p>
                                    </div>

                                    <div v-if="apt.notes" class="bg-purple-50 p-3 rounded-lg text-sm">
                                        <i class="fas fa-sticky-note text-purple-600 mr-2"></i>{{ apt.notes }}
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <Link
                                        v-if="apt.status === 'completada' && apt.diagnostic"
                                        :href="route('doctor.diagnostics.show', apt.diagnostic.id)"
                                        class="px-4 py-2 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-lg hover:shadow-lg transition-all font-medium whitespace-nowrap"
                                    >
                                        <i class="fas fa-file-medical mr-2"></i>Ver Diagnóstico
                                    </Link>

                                    <button
                                        v-if="canComplete(apt)"
                                        @click="openCompleteModal(apt)"
                                        class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:shadow-lg transition-all font-medium whitespace-nowrap"
                                    >
                                        <i class="fas fa-check-double mr-2"></i>Completar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Completar -->
            <div v-if="completeModal.show" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeCompleteModal">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-black bg-opacity-50"></div>
                    <div class="glass rounded-2xl p-8 max-w-md w-full relative z-10">
                        <div class="text-center mb-6">
                            <div class="w-16 h-16 bg-blue-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                                <i class="fas fa-check-double text-3xl text-blue-600"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Completar Cita</h3>
                            <p class="text-gray-600">Marca esta cita como completada</p>
                        </div>

                        <div v-if="completeModal.appointment" class="bg-gray-50 rounded-xl p-4 mb-6 space-y-2 text-sm">
                            <p><strong>Paciente:</strong> {{ completeModal.appointment.patient_name }}</p>
                            <p><strong>Fecha:</strong> {{ formatDate(completeModal.appointment.start_at) }}</p>
                            <p><strong>Hora:</strong> {{ formatTime(completeModal.appointment.start_at) }}</p>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Notas de la consulta (opcional)
                            </label>
                            <textarea
                                v-model="completeModal.notes"
                                rows="4"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Escribe observaciones o notas..."
                            ></textarea>
                        </div>

                        <div class="flex gap-3">
                            <button
                                @click="closeCompleteModal"
                                :disabled="completeModal.loading"
                                class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-medium disabled:opacity-50"
                            >
                                Cancelar
                            </button>
                            <button
                                @click="confirmComplete"
                                :disabled="completeModal.loading"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:shadow-lg transition-all font-medium disabled:opacity-50"
                            >
                                <i v-if="completeModal.loading" class="fas fa-spinner fa-spin mr-2"></i>
                                {{ completeModal.loading ? 'Guardando...' : 'Completar' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.glass {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(16px) saturate(180%);
    -webkit-backdrop-filter: blur(16px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
}

.blob {
    position: fixed;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.6;
    animation: blob 7s infinite;
    z-index: 0;
}

.blob-1 {
    width: 500px;
    height: 500px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    top: -10%;
    left: -10%;
}

.blob-2 {
    width: 400px;
    height: 400px;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    top: 50%;
    right: -10%;
    animation-delay: 2s;
}

.blob-3 {
    width: 450px;
    height: 450px;
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    bottom: -10%;
    left: 30%;
    animation-delay: 4s;
}

@keyframes blob {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
}
</style>
