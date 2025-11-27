<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminHeader from '@/Components/AdminHeader.vue';
import PatientCalendar from '@/Components/PatientCalendar.vue';

const props = defineProps({
    appointments: {
        type: Object,
        required: true
    },
    doctors: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

// Filtros
const searchTerm = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const doctorFilter = ref(props.filters.doctor || '');

// Estados para modales
const cancelModal = ref({
    show: false,
    appointment: null,
    loading: false,
    reason: ''
});

const rescheduleModal = ref({
    show: false,
    appointment: null,
    availability: [],
    duration: 30,
    newSlot: null,
    loading: false
});

const confirmModal = ref({
    show: false,
    appointment: null,
    loading: false,
    action: '' // 'accept' o 'reject'
});

// Funciones de formato
const formatDate = (datetime) => {
    const date = new Date(datetime);
    return date.toLocaleDateString('es-ES', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric'
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
const canCancel = (appointment) => {
    return (appointment.status === 'pendiente' || appointment.status === 'confirmada') && !isPast(appointment.start_at);
};

const canReschedule = (appointment) => {
    return (appointment.status === 'pendiente' || appointment.status === 'confirmada') && !isPast(appointment.start_at);
};

const canConfirm = (appointment) => {
    return appointment.status === 'pendiente' && !isPast(appointment.start_at);
};

// Aplicar filtros
const applyFilters = () => {
    router.get(route('panel.appointments.index'), {
        search: searchTerm.value,
        status: statusFilter.value,
        doctor: doctorFilter.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const clearFilters = () => {
    searchTerm.value = '';
    statusFilter.value = '';
    doctorFilter.value = '';
    applyFilters();
};

// Aceptar/Rechazar cita
const openConfirmModal = (appointment, action) => {
    confirmModal.value = {
        show: true,
        appointment: appointment,
        loading: false,
        action: action
    };
};

const closeConfirmModal = () => {
    confirmModal.value = {
        show: false,
        appointment: null,
        loading: false,
        action: ''
    };
};

const confirmAction = () => {
    confirmModal.value.loading = true;
    const routeName = confirmModal.value.action === 'accept' 
        ? 'panel.appointments.accept' 
        : 'panel.appointments.reject';
    
    router.post(route(routeName, confirmModal.value.appointment.slug), {}, {
        preserveState: true,
        onSuccess: () => {
            closeConfirmModal();
        },
        onError: () => {
            confirmModal.value.loading = false;
        }
    });
};

// Cancelar cita
const openCancelModal = (appointment) => {
    cancelModal.value = {
        show: true,
        appointment: appointment,
        loading: false,
        reason: ''
    };
};

const closeCancelModal = () => {
    cancelModal.value = {
        show: false,
        appointment: null,
        loading: false,
        reason: ''
    };
};

const confirmCancel = () => {
    cancelModal.value.loading = true;
    router.post(route('panel.appointments.cancel', cancelModal.value.appointment.slug), {
        reason: cancelModal.value.reason
    }, {
        preserveState: true,
        onSuccess: () => {
            closeCancelModal();
        },
        onError: () => {
            cancelModal.value.loading = false;
        }
    });
};

// Reagendar cita
const openRescheduleModal = async (appointment) => {
    rescheduleModal.value.loading = true;
    rescheduleModal.value.show = true;
    rescheduleModal.value.appointment = appointment;
    
    try {
        const response = await fetch(route('panel.appointments.reschedule-form', appointment.slug));
        const data = await response.json();
        
        if (data.success) {
            rescheduleModal.value.availability = data.availability || [];
            rescheduleModal.value.duration = data.duration || 30;
        }
    } catch (error) {
        console.error('Error al cargar disponibilidad:', error);
        alert('Error al cargar la disponibilidad del doctor');
        closeRescheduleModal();
    } finally {
        rescheduleModal.value.loading = false;
    }
};

const closeRescheduleModal = () => {
    rescheduleModal.value = {
        show: false,
        appointment: null,
        availability: [],
        duration: 30,
        newSlot: null,
        loading: false
    };
};

const confirmReschedule = () => {
    if (!rescheduleModal.value.newSlot) return;
    
    rescheduleModal.value.loading = true;
    router.post(route('panel.appointments.reschedule', rescheduleModal.value.appointment.slug), {
        new_start: rescheduleModal.value.newSlot
    }, {
        preserveState: true,
        onSuccess: () => {
            closeRescheduleModal();
        },
        onError: () => {
            rescheduleModal.value.loading = false;
        }
    });
};

// Estadísticas
const stats = computed(() => {
    const total = props.appointments.total || 0;
    const pending = props.appointments.data.filter(a => a.status === 'pendiente').length;
    const confirmed = props.appointments.data.filter(a => a.status === 'confirmada').length;
    const completed = props.appointments.data.filter(a => a.status === 'completada').length;
    
    return { total, pending, confirmed, completed };
});
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
        <!-- Header de navegación -->
        <AdminHeader subtitle="Gestión de Citas" />
        
        <div class="relative overflow-x-hidden">
            <!-- Blobs animados -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="blob blob-1"></div>
                <div class="blob blob-2"></div>
                <div class="blob blob-3"></div>
            </div>

            <!-- Contenido principal -->
            <div class="relative z-10 py-8 px-4 sm:px-6 lg:px-8">
                <div class="max-w-7xl mx-auto">
                    <!-- Header -->
                    <div class="mb-8 animate-fade-in-up">
                        <h1 class="text-4xl sm:text-5xl font-extrabold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">
                            <i class="fas fa-calendar-check mr-3"></i>
                            Gestión de Citas
                        </h1>
                        <p class="text-gray-600 text-lg">
                            Panel administrativo para gestionar todas las citas médicas
                        </p>
                    </div>

                    <!-- Estadísticas -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8 animate-fade-in-up" style="animation-delay: 0.1s">
                        <div class="glass rounded-xl p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Total Citas</p>
                                    <p class="text-3xl font-bold text-gray-900">{{ stats.total }}</p>
                                </div>
                                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-calendar text-white text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <div class="glass rounded-xl p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Pendientes</p>
                                    <p class="text-3xl font-bold text-yellow-600">{{ stats.pending }}</p>
                                </div>
                                <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-clock text-white text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <div class="glass rounded-xl p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Confirmadas</p>
                                    <p class="text-3xl font-bold text-green-600">{{ stats.confirmed }}</p>
                                </div>
                                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-check-circle text-white text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <div class="glass rounded-xl p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Completadas</p>
                                    <p class="text-3xl font-bold text-blue-600">{{ stats.completed }}</p>
                                </div>
                                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-check-double text-white text-2xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="glass rounded-2xl p-6 mb-8 animate-fade-in-up" style="animation-delay: 0.2s">
                        <div class="flex items-center gap-3 mb-4">
                            <i class="fas fa-filter text-blue-600 text-xl"></i>
                            <h2 class="text-xl font-bold text-gray-900">Filtros de búsqueda</h2>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <!-- Búsqueda por nombre/cédula -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-search mr-2"></i>Buscar paciente
                                </label>
                                <input
                                    v-model="searchTerm"
                                    type="text"
                                    placeholder="Nombre o cédula..."
                                    @keyup.enter="applyFilters"
                                    class="w-full px-4 py-2 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                />
                            </div>

                            <!-- Filtro por estado -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-tag mr-2"></i>Estado
                                </label>
                                <select
                                    v-model="statusFilter"
                                    @change="applyFilters"
                                    class="w-full px-4 py-2 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                >
                                    <option value="">Todos los estados</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="confirmada">Confirmada</option>
                                    <option value="rechazada">Rechazada</option>
                                    <option value="completada">Completada</option>
                                    <option value="cancelada">Cancelada</option>
                                </select>
                            </div>

                            <!-- Filtro por doctor -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user-md mr-2"></i>Doctor
                                </label>
                                <select
                                    v-model="doctorFilter"
                                    @change="applyFilters"
                                    class="w-full px-4 py-2 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                >
                                    <option value="">Todos los doctores</option>
                                    <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">
                                        {{ doctor.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button
                                @click="applyFilters"
                                class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all"
                            >
                                <i class="fas fa-search mr-2"></i>Buscar
                            </button>
                            <button
                                @click="clearFilters"
                                class="px-6 py-2 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all"
                            >
                                <i class="fas fa-times mr-2"></i>Limpiar
                            </button>
                        </div>
                    </div>

                    <!-- Lista de citas -->
                    <div v-if="appointments.data && appointments.data.length > 0" class="space-y-6 mb-8">
                        <div
                            v-for="appointment in appointments.data"
                            :key="appointment.id"
                            class="glass rounded-2xl p-6 hover:shadow-2xl transition-all duration-300 animate-fade-in-up"
                        >
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                                <!-- Info principal -->
                                <div class="flex-1 space-y-4">
                                    <!-- Status y badges -->
                                    <div class="flex flex-wrap items-center gap-3">
                                        <span :class="getStatusClass(appointment.status)" class="px-4 py-2 rounded-full text-sm font-semibold border-2 flex items-center gap-2">
                                            <i :class="['fas', getStatusIcon(appointment.status)]"></i>
                                            {{ getStatusText(appointment.status) }}
                                        </span>
                                        <span v-if="isToday(appointment.start_at)" class="px-4 py-2 rounded-full text-sm font-semibold bg-amber-100 text-amber-800 border-2 border-amber-300 animate-pulse">
                                            <i class="fas fa-star mr-1"></i>Hoy
                                        </span>
                                        <span v-if="isPast(appointment.start_at)" class="px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-600 border-2 border-gray-300">
                                            <i class="fas fa-history mr-1"></i>Pasada
                                        </span>
                                    </div>

                                    <!-- Información en grid -->
                                    <div class="grid sm:grid-cols-2 gap-4">
                                        <!-- Paciente -->
                                        <div class="bg-blue-50 rounded-xl p-4">
                                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">
                                                <i class="fas fa-user mr-1"></i>Paciente
                                            </p>
                                            <p class="font-bold text-gray-900 text-lg">{{ appointment.patient_name }}</p>
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-id-card mr-1"></i>{{ appointment.cedula_paciente }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-envelope mr-1"></i>{{ appointment.patient_email }}
                                            </p>
                                        </div>

                                        <!-- Doctor -->
                                        <div class="bg-purple-50 rounded-xl p-4">
                                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">
                                                <i class="fas fa-user-md mr-1"></i>Doctor
                                            </p>
                                            <p class="font-bold text-gray-900 text-lg">{{ appointment.doctor?.name }}</p>
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-stethoscope mr-1"></i>{{ appointment.doctor?.specialty }}
                                            </p>
                                        </div>

                                        <!-- Fecha -->
                                        <div class="bg-green-50 rounded-xl p-4">
                                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">
                                                <i class="fas fa-calendar mr-1"></i>Fecha
                                            </p>
                                            <p class="font-bold text-gray-900">{{ formatDate(appointment.start_at) }}</p>
                                        </div>

                                        <!-- Hora -->
                                        <div class="bg-amber-50 rounded-xl p-4">
                                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">
                                                <i class="fas fa-clock mr-1"></i>Hora
                                            </p>
                                            <p class="font-bold text-gray-900">{{ formatTime(appointment.start_at) }}</p>
                                        </div>
                                    </div>

                                    <!-- Notas -->
                                    <div v-if="appointment.notes" class="bg-purple-50 border-l-4 border-purple-600 rounded-lg p-4">
                                        <p class="text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-notes-medical mr-2 text-purple-600"></i>Notas
                                        </p>
                                        <p class="text-gray-900">{{ appointment.notes }}</p>
                                    </div>
                                </div>

                                <!-- Acciones -->
                                <div class="flex lg:flex-col gap-3 flex-wrap">
                                    <!-- Confirmar -->
                                    <button
                                        v-if="canConfirm(appointment)"
                                        @click="openConfirmModal(appointment, 'accept')"
                                        class="flex-1 lg:flex-none px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all"
                                    >
                                        <i class="fas fa-check mr-2"></i>Confirmar
                                    </button>

                                    <!-- Rechazar -->
                                    <button
                                        v-if="canConfirm(appointment)"
                                        @click="openConfirmModal(appointment, 'reject')"
                                        class="flex-1 lg:flex-none px-6 py-3 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all"
                                    >
                                        <i class="fas fa-times mr-2"></i>Rechazar
                                    </button>

                                    <!-- Reagendar -->
                                    <button
                                        v-if="canReschedule(appointment)"
                                        @click="openRescheduleModal(appointment)"
                                        class="flex-1 lg:flex-none px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all"
                                    >
                                        <i class="fas fa-calendar-alt mr-2"></i>Reagendar
                                    </button>

                                    <!-- Cancelar -->
                                    <button
                                        v-if="canCancel(appointment)"
                                        @click="openCancelModal(appointment)"
                                        class="flex-1 lg:flex-none px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all"
                                    >
                                        <i class="fas fa-ban mr-2"></i>Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estado vacío -->
                    <div v-else class="glass rounded-2xl p-12 text-center animate-fade-in-up">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-calendar-times text-4xl text-gray-400"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">No hay citas</h3>
                        <p class="text-gray-600">No se encontraron citas con los filtros seleccionados</p>
                    </div>

                    <!-- Paginación -->
                    <div v-if="appointments.links && appointments.links.length > 3" class="glass rounded-xl p-4 flex justify-center gap-2 animate-fade-in-up">
                        <template v-for="(link, index) in appointments.links" :key="index">
                            <button
                                v-if="link.url"
                                @click="router.visit(link.url)"
                                :class="[
                                    'px-4 py-2 rounded-lg font-semibold transition-all',
                                    link.active 
                                        ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg' 
                                        : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                ]"
                                v-html="link.label"
                            ></button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Modal de Confirmar/Rechazar -->
            <transition name="modal">
                <div v-if="confirmModal.show" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity bg-black/50 backdrop-blur-sm" @click="closeConfirmModal"></div>

                        <div class="inline-block align-bottom glass rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="p-6 sm:p-8">
                                <div :class="[
                                    'w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6',
                                    confirmModal.action === 'accept' ? 'bg-green-100' : 'bg-red-100'
                                ]">
                                    <i :class="[
                                        'fas text-3xl',
                                        confirmModal.action === 'accept' ? 'fa-check text-green-600' : 'fa-times text-red-600'
                                    ]"></i>
                                </div>

                                <h3 class="text-2xl font-bold text-center text-gray-900 mb-4">
                                    {{ confirmModal.action === 'accept' ? '¿Confirmar cita?' : '¿Rechazar cita?' }}
                                </h3>

                                <div v-if="confirmModal.appointment" class="bg-gray-50 rounded-xl p-4 mb-6 space-y-2">
                                    <p class="text-sm text-gray-600">
                                        <strong>Paciente:</strong> {{ confirmModal.appointment.patient_name }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <strong>Doctor:</strong> {{ confirmModal.appointment.doctor?.name }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <strong>Fecha:</strong> {{ formatDate(confirmModal.appointment.start_at) }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <strong>Hora:</strong> {{ formatTime(confirmModal.appointment.start_at) }}
                                    </p>
                                </div>

                                <p class="text-center text-gray-600 mb-6">
                                    {{ confirmModal.action === 'accept' 
                                        ? 'El paciente recibirá una notificación por email.' 
                                        : 'El paciente será notificado del rechazo por email.' }}
                                </p>

                                <div class="flex gap-3">
                                    <button
                                        @click="closeConfirmModal"
                                        :disabled="confirmModal.loading"
                                        class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all disabled:opacity-50"
                                    >
                                        Cancelar
                                    </button>
                                    <button
                                        @click="confirmAction"
                                        :disabled="confirmModal.loading"
                                        :class="[
                                            'flex-1 px-6 py-3 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all disabled:opacity-50',
                                            confirmModal.action === 'accept' 
                                                ? 'bg-gradient-to-r from-green-500 to-emerald-600' 
                                                : 'bg-gradient-to-r from-red-500 to-pink-600'
                                        ]"
                                    >
                                        <i v-if="confirmModal.loading" class="fas fa-spinner fa-spin mr-2"></i>
                                        {{ confirmModal.loading ? 'Procesando...' : (confirmModal.action === 'accept' ? 'Sí, confirmar' : 'Sí, rechazar') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>

            <!-- Modal de cancelación -->
            <transition name="modal">
                <div v-if="cancelModal.show" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity bg-black/50 backdrop-blur-sm" @click="closeCancelModal"></div>

                        <div class="inline-block align-bottom glass rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="p-6 sm:p-8">
                                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-exclamation-triangle text-3xl text-red-600"></i>
                                </div>

                                <h3 class="text-2xl font-bold text-center text-gray-900 mb-4">
                                    ¿Cancelar cita?
                                </h3>

                                <div v-if="cancelModal.appointment" class="bg-gray-50 rounded-xl p-4 mb-6 space-y-2">
                                    <p class="text-sm text-gray-600">
                                        <strong>Paciente:</strong> {{ cancelModal.appointment.patient_name }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <strong>Doctor:</strong> {{ cancelModal.appointment.doctor?.name }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <strong>Fecha:</strong> {{ formatDate(cancelModal.appointment.start_at) }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <strong>Hora:</strong> {{ formatTime(cancelModal.appointment.start_at) }}
                                    </p>
                                </div>

                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Motivo de cancelación (opcional)
                                    </label>
                                    <textarea
                                        v-model="cancelModal.reason"
                                        rows="3"
                                        placeholder="Explica el motivo de la cancelación..."
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all"
                                    ></textarea>
                                </div>

                                <p class="text-center text-gray-600 mb-6">
                                    El paciente será notificado sobre la cancelación.
                                </p>

                                <div class="flex gap-3">
                                    <button
                                        @click="closeCancelModal"
                                        :disabled="cancelModal.loading"
                                        class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all disabled:opacity-50"
                                    >
                                        No, mantener
                                    </button>
                                    <button
                                        @click="confirmCancel"
                                        :disabled="cancelModal.loading"
                                        class="flex-1 px-6 py-3 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all disabled:opacity-50"
                                    >
                                        <i v-if="cancelModal.loading" class="fas fa-spinner fa-spin mr-2"></i>
                                        {{ cancelModal.loading ? 'Cancelando...' : 'Sí, cancelar' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>

            <!-- Modal de reagendamiento -->
            <transition name="modal">
                <div v-if="rescheduleModal.show" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity bg-black/50 backdrop-blur-sm" @click="closeRescheduleModal"></div>

                        <div class="inline-block align-bottom glass rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                            <div class="p-6 sm:p-8">
                                <h3 class="text-2xl font-bold text-center text-gray-900 mb-6">
                                    <i class="fas fa-calendar-alt mr-2 text-amber-600"></i>
                                    Reagendar cita
                                </h3>

                                <div v-if="rescheduleModal.appointment" class="bg-amber-50 border-l-4 border-amber-600 rounded-lg p-4 mb-6">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Cita actual:</p>
                                    <p class="text-gray-900">
                                        <strong>{{ formatDate(rescheduleModal.appointment.start_at) }}</strong> a las <strong>{{ formatTime(rescheduleModal.appointment.start_at) }}</strong>
                                    </p>
                                </div>

                                <div v-if="rescheduleModal.loading" class="text-center py-12">
                                    <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
                                    <p class="text-gray-600">Cargando disponibilidad...</p>
                                </div>

                                <div v-else>
                                    <PatientCalendar
                                        :availability="rescheduleModal.availability"
                                        :duration="rescheduleModal.duration"
                                        @select="rescheduleModal.newSlot = $event"
                                    />

                                    <div v-if="rescheduleModal.newSlot" class="mt-6 bg-green-50 border-l-4 border-green-600 rounded-lg p-4">
                                        <p class="text-sm font-medium text-gray-700 mb-2">Nueva hora seleccionada:</p>
                                        <p class="text-lg font-bold text-gray-900">
                                            {{ formatDate(rescheduleModal.newSlot) }} a las {{ formatTime(rescheduleModal.newSlot) }}
                                        </p>
                                    </div>

                                    <div class="flex gap-3 mt-6">
                                        <button
                                            @click="closeRescheduleModal"
                                            :disabled="rescheduleModal.loading"
                                            class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all disabled:opacity-50"
                                        >
                                            Cancelar
                                        </button>
                                        <button
                                            @click="confirmReschedule"
                                            :disabled="!rescheduleModal.newSlot || rescheduleModal.loading"
                                            class="flex-1 px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <i v-if="rescheduleModal.loading" class="fas fa-spinner fa-spin mr-2"></i>
                                            {{ rescheduleModal.loading ? 'Reagendando...' : 'Confirmar reagendamiento' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<style scoped>
.glass {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(16px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.5;
    animation: blob 7s infinite;
}

.blob-1 {
    width: 400px;
    height: 400px;
    background: linear-gradient(180deg, #3b82f6 0%, #8b5cf6 100%);
    top: -200px;
    left: -200px;
}

.blob-2 {
    width: 350px;
    height: 350px;
    background: linear-gradient(180deg, #6366f1 0%, #ec4899 100%);
    top: 40%;
    right: -150px;
    animation-delay: 2s;
}

.blob-3 {
    width: 300px;
    height: 300px;
    background: linear-gradient(180deg, #8b5cf6 0%, #ec4899 100%);
    bottom: -100px;
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

@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.6s ease-out forwards;
}

.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .glass,
.modal-leave-active .glass {
    transition: transform 0.3s ease;
}

.modal-enter-from .glass,
.modal-leave-to .glass {
    transform: scale(0.9);
}
</style>
