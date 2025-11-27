<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    appointments: {
        type: Object,
        required: true
    },
    doctor: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

// Estado actual
const selectedDate = ref(new Date().toISOString().split('T')[0]);
const statusFilter = ref(props.filters.status || '');
const viewMode = ref('today'); // 'today', 'week', 'all'

// Estados para modales
const completeModal = ref({
    show: false,
    appointment: null,
    loading: false,
    notes: ''
});

// Funciones de formato
const formatDate = (datetime) => {
    const date = new Date(datetime);
    return date.toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatShortDate = (datetime) => {
    const date = new Date(datetime);
    return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short'
    });
};

const formatTime = (datetime) => {
    const date = new Date(datetime);
    return date.toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit'
    });
};

const isPast = (datetime) => {
    return new Date(datetime) < new Date();
};

const isToday = (datetime) => {
    const today = new Date();
    const date = new Date(datetime);
    return today.toDateString() === date.toDateString();
};

const getDayOfWeek = (datetime) => {
    const date = new Date(datetime);
    const days = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    return days[date.getDay()];
};

// Status helpers
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
    // Permitir completar si está confirmada y es de hoy o de días anteriores
    if (appointment.status !== 'confirmada') {
        return false;
    }
    
    // Extraer solo la fecha de la cadena ISO (antes de la T)
    // Ejemplo: "2025-11-26T10:00:00.000000Z" -> "2025-11-26"
    const aptDateStr = appointment.start_at.substring(0, 10);
    
    // Obtener fecha de hoy en formato YYYY-MM-DD en zona horaria local
    const today = new Date();
    const todayStr = today.getFullYear() + '-' + 
                     String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                     String(today.getDate()).padStart(2, '0');
    
    // Puede completar si es hoy o anterior
    return aptDateStr <= todayStr;
};

// Agrupar citas por fecha
const groupedAppointments = computed(() => {
    const groups = {};
    let filtered = [...props.appointments.data];
    
    // Filtrar por estado si hay filtro activo
    if (statusFilter.value) {
        filtered = filtered.filter(a => a.status === statusFilter.value);
    }
    
    // Ordenar por fecha (más recientes primero)
    filtered.sort((a, b) => new Date(a.start_at) - new Date(b.start_at));
    
    // Agrupar por fecha
    filtered.forEach(appointment => {
        const date = new Date(appointment.start_at).toISOString().split('T')[0];
        if (!groups[date]) {
            groups[date] = [];
        }
        groups[date].push(appointment);
    });
    
    return groups;
});

// Citas de hoy
const todayAppointments = computed(() => {
    const today = new Date().toISOString().split('T')[0];
    return groupedAppointments.value[today] || [];
});

// Próximas citas (próximos 7 días)
const upcomingDates = computed(() => {
    const dates = Object.keys(groupedAppointments.value).sort();
    const today = new Date().toISOString().split('T')[0];
    return dates.filter(date => date >= today).slice(0, 7);
});

// Estadísticas
const stats = computed(() => {
    const today = new Date().toISOString().split('T')[0];
    const todayCitas = groupedAppointments.value[today] || [];
    
    return {
        today: todayCitas.length,
        pending: props.appointments.data.filter(a => a.status === 'pendiente').length,
        confirmed: props.appointments.data.filter(a => a.status === 'confirmada').length,
        completed: props.appointments.data.filter(a => a.status === 'completada').length
    };
});

// Aplicar filtros
const applyFilters = () => {
    router.get(route('doctor.appointments.index'), {
        status: statusFilter.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const clearFilters = () => {
    statusFilter.value = '';
    applyFilters();
};

// Completar cita
const openCompleteModal = (appointment) => {
    completeModal.value = {
        show: true,
        appointment: appointment,
        loading: false,
        notes: appointment.notes || ''
    };
};

const closeCompleteModal = () => {
    completeModal.value = {
        show: false,
        appointment: null,
        loading: false,
        notes: ''
    };
};

const confirmComplete = () => {
    completeModal.value.loading = true;
    router.post(route('doctor.appointments.complete', completeModal.value.appointment.slug), {
        notes: completeModal.value.notes
    }, {
        preserveState: true,
        onSuccess: () => {
            closeCompleteModal();
        },
        onError: () => {
            completeModal.value.loading = false;
        }
    });
};

// Cambiar vista
const changeView = (mode) => {
    viewMode.value = mode;
};

</script>

<template>
    <AppLayout title="Mis Citas">
        <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <!-- Blobs de fondo -->
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>
            <div class="blob blob-3"></div>

            <div class="max-w-7xl mx-auto relative z-10">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        <i class="fas fa-calendar-alt mr-3"></i>
                        Panel del Doctor
                    </h1>
                    <p class="mt-2 text-gray-600">Dr. {{ doctor.name }} - {{ doctor.specialty }}</p>
                </div>

                <!-- Estadísticas -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <!-- Hoy -->
                    <div class="glass p-6 rounded-2xl hover:shadow-xl transition-all duration-300 cursor-pointer" @click="changeView('today')">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Hoy</p>
                                <p class="text-3xl font-bold text-indigo-600">{{ stats.today }}</p>
                            </div>
                            <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-calendar-day text-white text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Pendientes -->
                    <div class="glass p-6 rounded-2xl hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Pendientes</p>
                                <p class="text-3xl font-bold text-yellow-600">{{ stats.pending }}</p>
                            </div>
                            <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-clock text-white text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Confirmadas -->
                    <div class="glass p-6 rounded-2xl hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Confirmadas</p>
                                <p class="text-3xl font-bold text-green-600">{{ stats.confirmed }}</p>
                            </div>
                            <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-check-circle text-white text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Completadas -->
                    <div class="glass p-6 rounded-2xl hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Completadas</p>
                                <p class="text-3xl font-bold text-blue-600">{{ stats.completed }}</p>
                            </div>
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-check-double text-white text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtro de Estado -->
                <div class="glass p-4 rounded-2xl mb-8">
                    <div class="flex flex-wrap gap-4 items-center">
                        <label class="text-sm font-medium text-gray-700">
                            <i class="fas fa-filter mr-2"></i>Filtrar por estado:
                        </label>
                        <select
                            v-model="statusFilter"
                            @change="applyFilters"
                            class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Todos</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="confirmada">Confirmada</option>
                            <option value="completada">Completada</option>
                            <option value="rechazada">Rechazada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                        <button
                            v-if="statusFilter"
                            @click="clearFilters"
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all duration-300 text-sm"
                        >
                            <i class="fas fa-times mr-2"></i>Limpiar
                        </button>
                    </div>
                </div>

                <!-- Vista de Calendario -->
                <div class="space-y-6">
                    <!-- Citas de Hoy (Destacadas) -->
                    <div v-if="todayAppointments.length > 0" class="glass p-6 rounded-2xl border-2 border-indigo-500">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-star text-yellow-500 mr-3 animate-pulse"></i>
                                Citas de Hoy
                            </h2>
                            <span class="px-4 py-2 bg-indigo-100 text-indigo-800 rounded-full font-semibold">
                                {{ todayAppointments.length }} cita{{ todayAppointments.length !== 1 ? 's' : '' }}
                            </span>
                        </div>

                        <div class="space-y-3">
                            <div
                                v-for="appointment in todayAppointments"
                                :key="appointment.id"
                                class="bg-white p-5 rounded-xl border-l-4 hover:shadow-lg transition-all duration-300"
                                :class="{
                                    'border-yellow-500': appointment.status === 'pendiente',
                                    'border-green-500': appointment.status === 'confirmada',
                                    'border-blue-500': appointment.status === 'completada',
                                    'border-red-500': appointment.status === 'rechazada',
                                    'border-gray-500': appointment.status === 'cancelada'
                                }"
                            >
                                <div class="flex flex-col lg:flex-row gap-4">
                                    <!-- Hora -->
                                    <div class="flex-shrink-0 text-center lg:text-left">
                                        <div class="inline-block lg:block bg-gradient-to-br from-indigo-500 to-purple-600 text-white px-4 py-3 rounded-xl">
                                            <i class="fas fa-clock text-xl mb-1"></i>
                                            <p class="text-2xl font-bold">{{ formatTime(appointment.start_at) }}</p>
                                        </div>
                                    </div>

                                    <!-- Información -->
                                    <div class="flex-1 space-y-2">
                                        <div class="flex items-center justify-between flex-wrap gap-2">
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                <i class="fas fa-user-circle text-blue-600 mr-2"></i>
                                                {{ appointment.patient_name }}
                                            </h3>
                                            <span
                                                :class="getStatusClass(appointment.status)"
                                                class="px-3 py-1 rounded-full text-xs font-medium border flex items-center gap-1"
                                            >
                                                <i class="fas" :class="getStatusIcon(appointment.status)"></i>
                                                {{ getStatusText(appointment.status) }}
                                            </span>
                                        </div>

                                        <div class="grid sm:grid-cols-2 gap-2 text-sm">
                                            <p class="text-gray-600">
                                                <i class="fas fa-id-card mr-2"></i>{{ appointment.cedula_paciente }}
                                            </p>
                                            <p class="text-gray-600">
                                                <i class="fas fa-envelope mr-2"></i>{{ appointment.patient_email }}
                                            </p>
                                        </div>

                                        <div v-if="appointment.notes" class="bg-purple-50 p-3 rounded-lg text-sm text-gray-700">
                                            <i class="fas fa-sticky-note text-purple-600 mr-2"></i>{{ appointment.notes }}
                                        </div>
                                    </div>

                                    <!-- Acciones -->
                                    <div class="flex lg:flex-col gap-2 flex-wrap">
                                        <button
                                            v-if="canComplete(appointment)"
                                            @click="openCompleteModal(appointment)"
                                            class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-medium text-sm whitespace-nowrap"
                                        >
                                            <i class="fas fa-check-double mr-2"></i>Completar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Próximas Citas (Por Día) -->
                    <div v-if="upcomingDates.length > 0">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-calendar-week text-blue-600 mr-3"></i>
                            Próximas Citas
                        </h2>

                        <div class="space-y-4">
                            <div
                                v-for="date in upcomingDates"
                                :key="date"
                                class="glass p-5 rounded-2xl"
                            >
                                <!-- Encabezado del día -->
                                <div class="flex items-center justify-between mb-4 pb-3 border-b-2 border-gray-200">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">
                                            {{ formatDate(groupedAppointments[date][0].start_at) }}
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            {{ getDayOfWeek(groupedAppointments[date][0].start_at) }}
                                        </p>
                                    </div>
                                    <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full font-semibold text-sm">
                                        {{ groupedAppointments[date].length }} cita{{ groupedAppointments[date].length !== 1 ? 's' : '' }}
                                    </span>
                                </div>

                                <!-- Lista de citas del día -->
                                <div class="space-y-3">
                                    <div
                                        v-for="appointment in groupedAppointments[date]"
                                        :key="appointment.id"
                                        class="bg-white p-4 rounded-xl border-l-4 hover:shadow-lg transition-all duration-300"
                                        :class="{
                                            'border-yellow-500': appointment.status === 'pendiente',
                                            'border-green-500': appointment.status === 'confirmada',
                                            'border-blue-500': appointment.status === 'completada',
                                            'border-red-500': appointment.status === 'rechazada',
                                            'border-gray-500': appointment.status === 'cancelada'
                                        }"
                                    >
                                        <div class="flex flex-col lg:flex-row gap-4">
                                            <!-- Hora -->
                                            <div class="flex-shrink-0">
                                                <div class="inline-block bg-gradient-to-br from-blue-500 to-indigo-600 text-white px-4 py-2 rounded-lg">
                                                    <i class="fas fa-clock mr-2"></i>
                                                    <span class="text-lg font-bold">{{ formatTime(appointment.start_at) }}</span>
                                                </div>
                                            </div>

                                            <!-- Información -->
                                            <div class="flex-1 space-y-2">
                                                <div class="flex items-center justify-between flex-wrap gap-2">
                                                    <h4 class="text-lg font-semibold text-gray-900">
                                                        <i class="fas fa-user-circle text-blue-600 mr-2"></i>
                                                        {{ appointment.patient_name }}
                                                    </h4>
                                                    <span
                                                        :class="getStatusClass(appointment.status)"
                                                        class="px-3 py-1 rounded-full text-xs font-medium border flex items-center gap-1"
                                                    >
                                                        <i class="fas" :class="getStatusIcon(appointment.status)"></i>
                                                        {{ getStatusText(appointment.status) }}
                                                    </span>
                                                </div>

                                                <div class="grid sm:grid-cols-2 gap-2 text-sm text-gray-600">
                                                    <p><i class="fas fa-id-card mr-2"></i>{{ appointment.cedula_paciente }}</p>
                                                    <p><i class="fas fa-envelope mr-2"></i>{{ appointment.patient_email }}</p>
                                                </div>

                                                <div v-if="appointment.notes" class="bg-purple-50 p-2 rounded text-sm text-gray-700">
                                                    <i class="fas fa-sticky-note text-purple-600 mr-2"></i>{{ appointment.notes }}
                                                </div>
                                            </div>

                                            <!-- Acciones -->
                                            <div class="flex lg:flex-col gap-2 flex-wrap">
                                                <button
                                                    v-if="canComplete(appointment)"
                                                    @click="openCompleteModal(appointment)"
                                                    class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-medium text-sm whitespace-nowrap"
                                                >
                                                    <i class="fas fa-check-double mr-2"></i>Completar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sin citas -->
                    <div v-if="todayAppointments.length === 0 && upcomingDates.length === 0" class="glass p-12 rounded-2xl text-center">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-calendar-times text-6xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No hay citas</h3>
                        <p class="text-gray-500">No tienes citas programadas.</p>
                    </div>
                </div>
            </div>

            <!-- Modal de Completar -->
            <div
                v-if="completeModal.show"
                class="fixed inset-0 z-50 overflow-y-auto"
                @click.self="closeCompleteModal"
            >
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
                    <div class="glass rounded-2xl p-8 max-w-md w-full relative z-10 animate-scale-in">
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
                                class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-300 font-medium disabled:opacity-50"
                            >
                                Cancelar
                            </button>
                            <button
                                @click="confirmComplete"
                                :disabled="completeModal.loading"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium disabled:opacity-50"
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
    animation-delay: 0s;
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
    0%, 100% {
        transform: translate(0, 0) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
}

@keyframes scale-in {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-scale-in {
    animation: scale-in 0.3s ease-out;
}
</style>
